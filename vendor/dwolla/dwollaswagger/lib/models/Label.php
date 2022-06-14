<?php
/**
 *  Copyright 2015 SmartBear Software
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

/**
 *
 *
 * NOTE: This class is auto generated by the swagger code generator program. Do not edit the class manually.
 *
 */

namespace DwollaSwagger\models;

use \ArrayAccess;

class Label implements ArrayAccess {
  static $swaggerTypes = array(
      '_links' => 'map[string,HalLink]',
      '_embedded' => 'object',
      'id' => 'string',
      'created' => 'DateTime',
      'amount' => 'Amount'
  );

  static $attributeMap = array(
      '_links' => '_links',
      '_embedded' => '_embedded',
      'id' => 'id',
      'created' => 'created',
      'amount' => 'amount'
  );


  public $_links; /* map[string,HalLink] */
  public $_embedded; /* object */
  public $id; /* string */
  public $created; /* DateTime */
  public $amount; /* Amount */

  public function __construct(array $data = null) {
    $this->_links = isset($data["_links"]) ? $data["_links"] : null;
    $this->_embedded = isset($data["_embedded"]) ? $data["_embedded"] : null;
    $this->id = isset($data["id"]) ? $data["id"] : null;
    $this->created = isset($data["created"]) ? $data["created"] : null;
    $this->amount = isset($data["amount"]) ? $data["amount"] : null;
  }

  public function offsetExists($offset) {
    return isset($this->$offset);
  }

  public function offsetGet($offset) {
    return $this->$offset;
  }

  public function offsetSet($offset, $value) {
    $this->$offset = $value;
  }

  public function offsetUnset($offset) {
    unset($this->$offset);
  }
}
