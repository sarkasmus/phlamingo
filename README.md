# Phlamingo
## New modern PHP Framework
Phlamingo is a new modern php framework in early development state. I have primary ideas about usage for programmers. I am trying to make robust, secure, all-in-one but still simple to use and intuitive. Im trying to overcome lacks of other PHP frameworks and try to really save time of programmers

## Structure
```
phlamingo
  |-- app/
      |-- main/
  |-- phlamingo/
      |-- core/
      |-- http/
      |-- di/
      |-- nebula/
  |-- www/
      |-- .htaccess
      |-- index.php
```
## Routing and Controllers
For create controller in package Main create class in /app/main/controllers
```php
class HomeController extends BaseController
{
    public function HomeAction()
    {
        return new Response("Hello world");
    }
```
For register route in the Router use
```php
$router->AddRoute("home/", ["controller" => HomeController::class, "action" => "HomeAction"]);
```
Easy right ?

## Dependency injection
Dependency injection is solved in Phlamingo internally in class (which inherits from Core/Object). It solves many problems with traditional way of injecting dependencies
which violates Law of Demeter (you have to use container instead of use new operator) and makes application extremely dependent on container.
```php
/**
 * @Service serviceName
 */
public $service;
```
Or you can use container certainly but it's not best practice
```php
$this->Container->Get("serviceName");
```
For registering you can use callback function or class which inherits from FactoryAbstract (will be implemented soon)
```php
$this->Container->AddService("serviceName", function (){...});
class ServiceFactory extends BaseFactory
{
    public function Make()
    {
        // Implement factory here
    }
}
$this->Container->AddService("serviceName", new ServiceFactory());
```
## Nebula
Imagine you code template in HTML and you would add some tags to it. Do you hate Javascript? Would it be better to write client scripts which
you can test, debug and uses OOP? Yes? Thats Nebula!

It uses XML modification to code template which is converted by XSLT to HTML
Nebula XML uses namespaces n for nebula tags, h for html tags (so you can still use HTML naturally). And the best part: you can create your 
own elements, attributes in your namespaces and
set them behavior. You can create for example <p:myPayment> for render your payment gate or <b:grid> for bootstrap grid.
I have also some ideas about Stylesheets and Javascript.
