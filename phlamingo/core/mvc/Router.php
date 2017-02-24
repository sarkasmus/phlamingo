<?php

    /**
     * @author Michal Doubek <michal@doubkovi.cz>
     * @license LGPL 3
     *
     * This code is distributed under LGPL license version 3
     * For full license information view LICENSE file which is
     * Distributed with this source code
     *
     * This source code is part of Phlamingo project
     */

    namespace Phlamingo\Core\MVC;

    use Phlamingo\Core\MVC\Exceptions\RouterException;
        use Phlamingo\Core\Object;
        use Phlamingo\HTTP\Request;

    /**
     * Router converts requested URLs to callable actions in application.
     *
     * @Service Router
     */
    class Router extends Object
    {
        /**
         * List of routes.
         *
         * @var array
         */
        protected $routes = [];

        /**
         * Finds route by request and returns controller and controller
         * action with parameters given in request.
         *
         * -- Method is complex follow line comments to understand
         *
         * @param Request|string $request HTTP Request
         *
         * @throws RouterException When haven't been found any routes
         *
         * @return array Event -> controller, controller action and params
         */
        public function Route($request) : array
        {
            // Output varibles, selected matching route and params in URI. varibles are used
            // as cach in proccess
            $finalRoute = null;
            $params = [];

            // Setup request uri
            $requestUri = null;
            if ($request instanceof Request) {
                $requestUri = $request->URI;
            } else {
                $requestUri = $request;
            }

            // Browse all routes
            foreach ($this->routes as $route) {
                // Divide route mask and real URI to parts by / delimiter e.g "user/5" to ["user", 5]
                $mask = trim($route['mask'], '/');
                $routeParts = explode('/', $mask);
                $uriParts = explode('/', trim($requestUri, '/'));

                // Browse all parts of route. Route can has more parts then URI because it can has optional parameters
                while (!empty($routeParts)) {
                    // Iterate until the routeParts array will be empty
                    $routePart = array_shift($routeParts);
                    $routePart = $routePart !== null ? $routePart : '{empty}';

                    // Iterate URI parts
                    $uriPart = array_shift($uriParts);
                    $uriPart = $uriPart !== null ? $uriPart : '{empty}';

                    // Route part is an mask expression e.g: {en|de} or {string}, {int:default}
                    if (substr($routePart, 0, 1) === '{' and substr($routePart, -1) === '}') {
                        // Remove brackets from expression
                        $expression = trim($routePart, '{}');

                        // Checks when expression matches URI value e.g: {int} matches: 5, 90
                        // {string} matches any text {en|de} matches "en" or "de"
                        if ($this->expressionMatch($expression, $uriPart)) {
                            // If value is empty it will be replaced by default value from expression
                            // or else will return $uriPart
                            $params[] = $this->expressionValue($expression, $uriPart);
                            $finalRoute = $route;
                            continue;
                        } else {
                            // Clear output varibles from cached values
                            $finalRoute = null;
                            $params = [];
                            break; // End cycle because route doesn't match
                        }
                    }
                    // Route part and uriPart are equal e.g: /ArticleController/{int} == /ArticleController/5 -
                    // controllers are same but its not limited just to controllers and action
                    elseif ($routePart == $uriPart) {
                        $finalRoute = $route;
                        continue;
                    } else {
                        // Clear output varibles from cached values
                        $finalRoute = null;
                        $params = [];
                        break; // End cycle because route doesn't match
                    }
                }

                // If route is fully matching URI cycle ends
                if ($finalRoute !== null) {
                    break;
                }
            }

            // If route has been found:
            if ($finalRoute !== null) {
                // Return event
                return [
                    'controller' => $finalRoute['event']['controller'],
                    'action'     => $finalRoute['event']['action'],
                    'params'     => $params,
                ];
            } else {
                // Throws exception when no routes wasn't found
                throw new RouterException("No routes wasn't found for your request");
            }
        }

        /**
         * Adds new route to list.
         *
         * @param string $mask  Mask of route (e.g: "controller/action", "user/{int:default}")
         * @param array  $event Event, array containing controller and action to call
         */
        public function addRoute(string $mask, array $event)
        {
            $this->routes[] = ['mask' => trim($mask, '/'), 'event' => $event];
        }

        /**
         * Generates URL from event (controller, action) and its params.
         *
         * @param array       $event  Event
         * @param array|mixed $params Parameters for create URL from route
         *
         * @throws RouterException When parameters are invalid or any route haven't been found
         *
         * @return string Generated URL
         */
        public function generateURL(array $event, ...$params) : string
        {
            foreach ($this->routes as $route) {
                if ($route['event'] == $event) {
                    $route = explode('/', $route['mask']);

                    $result = [];
                    $paramIterator = 0;
                    foreach ($route as $routePart) {
                        if (substr($routePart, 0, 1) === '{' and substr($routePart, -1) === '}') {
                            if ($this->expressionMatch($routePart, $params[$paramIterator])) {
                                $result[] = $params[$paramIterator];
                                $paramIterator++;
                            } else {
                                throw new RouterException("Params don't matches event's route");
                            }
                        } else {
                            $result[] = $routePart;
                        }
                    }

                    return DOMAIN.'/'.implode('/', $result);
                }
            }

            throw new RouterException("Route for your event wasn't found");
        }

        /**
         * Sets default homepage event.
         *
         * @param array $event Event
         */
        public function setHomepage(array $event)
        {
            $this->routes[] = ['mask' => '', 'event' => $event];
        }

        /**
         * Checks if expression matches value e.g: exp: {int} matches all numbers, {1-100}
         * matches numbers between 1 and 100 {string:default} matches strings and is optional,
         * {en|de} matches "en" or "de".
         *
         * @param string $expression Expression to match
         * @param mixed  $uriPart    Value to match by expression
         *
         * @return bool Matches or not
         */
        protected function expressionMatch(string $expression, $uriPart) : bool
        {
            if ($expression == 'empty') {
                return true;
            } else {
                $results = [];
                $subExpressions = explode('|', $expression);
                foreach ($subExpressions as $subExpression) {
                    $results[] = $this->parseSubExpression($subExpression, $uriPart);
                }

                return in_array(true, $results);
            }
        }

        /**
         * Checks if subexpression matches value.
         *
         * -- This method is comples follow line comments to understand
         *
         * @param string $subExpression Subexpression
         * @param mixed  $uriPart       Value to match
         *
         * @return bool Matches or not
         */
        protected function parseSubExpression(string $subExpression, $uriPart) : bool
        {
            $types = ['string', 'int', 'number'];
            if (in_array($subExpression, $types)) {
                return $this->expressionTypes($subExpression, $uriPart);
            } elseif (strpos($subExpression, '-')) {
                $subExpression = explode('-', $subExpression);
                if ((int) $uriPart >= (int) $subExpression[0] and (int) $uriPart <= (int) $subExpression[1]) {
                    return true;
                } else {
                    return false;
                }
            } elseif (strpos($subExpression, ':') !== false) {
                $subExpression = explode(':', $subExpression);
                if ($uriPart == '{empty}') {
                    return true;
                } else {
                    // Calling self method
                    return $this->parseSubExpression($subExpression[0], $uriPart);
                }
            } elseif ($subExpression == $uriPart) {
                return true;
            } else {
                return false;
            }
        }

        /**
         * Checks if value matches type (string, integer).
         *
         * @param string $expression Expression type e.g: {string}, {int}
         * @param mixed  $uriPart    Value to match
         *
         * @return bool Is value of type in expression or not
         */
        protected function expressionTypes(string $expression, $uriPart) : bool
        {
            if ($expression == 'string' and is_string($uriPart)) {
                return true;
            } elseif (($expression == 'int' or $expression == 'number') and is_numeric($uriPart)) {
                return true;
            } else {
                return false;
            }
        }

        /**
         * Returns value of an expression.
         *
         * @param string $expression Expression
         * @param mixed  $uriPart    Value of expression
         *
         * @return mixed Value or if it is empty it returns default value of expression
         */
        protected function expressionValue(string $expression, $uriPart)
        {
            if (strpos($expression, ':') and $uriPart == '{empty}') {
                return explode(':', $expression)[1];
            } else {
                return $uriPart;
            }
        }
    }
