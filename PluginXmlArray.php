<?php
class PluginXmlArray{
  public function xml_to_array($file){
    $file = wfGlobals::getAppDir().$file;
    $doc = new DOMDocument();
    $doc->load($file);
    return $this->parse_xml_to_array($doc);
  }
  private function parse_xml_to_array($data){
    $result = array();
    /**
     * Attributes
     */
    if ($data->hasAttributes()) {
      $attrs = $data->attributes;
      foreach ($attrs as $attr) {
        $result['@attributes'][$attr->name] = $attr->value;
      }
    }
    /**
     * Child nodes
     */
    if ($data->hasChildNodes()) {
      $children = $data->childNodes;
      if ($children->length == 1) {
        $child = $children->item(0);
        if ($child->nodeType == XML_TEXT_NODE) {
          $result['_value'] = $child->nodeValue;
          return count($result) == 1 ? $result['_value'] : $result;
        }
      }
      $groups = array();
      foreach ($children as $child) {
        if (!isset($result[$child->nodeName])) {
          /**
           * More data
           */
          $result[$child->nodeName] = $this->parse_xml_to_array($child);
        } else {
          if (!isset($groups[$child->nodeName])) {
            $result[$child->nodeName] = array($result[$child->nodeName]);
            $groups[$child->nodeName] = 1;
          }
          /**
           * More data
           */
          $result[$child->nodeName][] = $this->parse_xml_to_array($child);
        }
      }
    }
    return $result;
  }  
}
