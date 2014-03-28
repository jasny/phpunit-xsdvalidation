<?php
namespace Jasny\PHPUnit\Constraint;

/**
 * PHPUnit constraint to validate against XSD
 */
class XSDValidation extends \PHPUnit_Framework_Constraint
{
    protected $schema;
    
    
    /**
     * Class constructor
     * 
     * @param string $schema   filename or source
     */
    public function __construct($schema)
    {
        parent::__construct();
        
        $this->schema = $schema;
    }
    
    /**
     * Returns a string representation of the constraint.
     *
     * @return string
     */
    public function toString()
    {
        return "validates against XSD schema" . ($this->schemaIsXml() ? '' : " " . $this->schema);
    }
    
    /**
     * Evaluates the constraint for parameter $other. Returns true if the
     * constraint is met, false otherwise.
     *
     * This method can be overridden to implement the evaluation algorithm.
     *
     * @param  \DomDocument|\SimpleXMLElement|string $other  XML to validate.
     * @return boolean
     */
    protected function matches($other)
    {
        if ($other instanceof \SimpleXMLElement) {
            $dom = new \DOMDocument('1.0');
            $dom->appendChild($dom->importNode(dom_import_simplexml($other), true));
        } elseif (!$other instanceof \DOMDocument) {
            $dom = \DOMDocument::load($other);
        } else {
            $dom = $other;
        }

        return $this->schemaIsXml() ? $dom->schemaValidateSource($this->schema) : $dom->schemaValidate($this->schema);
    }
    
    /**
     * Check if schema contains a '<' character.
     * 
     * @return string
     */
    protected function schemaIsXml()
    {
        return strpos($this->schema, '<') !== false;
    }
}
