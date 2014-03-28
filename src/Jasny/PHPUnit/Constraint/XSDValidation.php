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
        return "validates against XSD schema" . ($this->schemaIsXml() ? '' : " " . $this->source);
    }
    
    /**
     * Evaluates the constraint for parameter $other. Returns true if the
     * constraint is met, false otherwise.
     *
     * This method can be overridden to implement the evaluation algorithm.
     *
     * @param  \DomDocument|string $other  XML to validate.
     * @return boolean
     */
    protected function matches($other)
    {
        if (!$other instanceof \DOMDocument) $other = \DOMDocument::load($other);
        return $this->schemaIsXml() ? $other->schemaValidate($this->schema) : $other->schemaValidateSource($this->schema);
    }
    
    /**
     * Check if schema contains a '<' character.
     * 
     * @return string
     */
    protected function schemaIsXml()
    {
        return strpos($this->source, '<') !== false;
    }
}
