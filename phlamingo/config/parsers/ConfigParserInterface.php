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

namespace Phlamingo\Config\Parsers;

interface ConfigParserInterface
{
    public function parse(string $code);

    public function parseFile(string $path);

    public function dump(array $data);

    public function dumpToFile(array $data, string $path);
}
