<?php
namespace Jasny\PHPUnit\Constraint;

use PHPUnit\Framework\Constraint\Constraint;

/**
 * PHPUnit constraint to validate against XSD
 */
class XSDValidation extends Constraint
{
    /**
     * XSD schema filename or source
     * @var string
     */
    protected $schema;

    /**
     * LibXML errors
     * @var array
     */
    protected $errors = [];


    /**
     * XSDValidation constructor.
     *
     * @param string $schema   filename or source
     * @throws \Exception
     */
    public function __construct($schema)
    {
        if (method_exists(get_parent_class(), '__construct')) {
            parent::__construct();
        }

        $this->schema = $schema;
        if (!$this->schemaIsXml() && !file_exists($this->schema)) {
            throw new \Exception("Schema {$this->schema} doesn't exist");
        }
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
        libxml_use_internal_errors(true);

        if ($other instanceof \SimpleXMLElement) {
            $dom = new \DOMDocument('1.0');
            $dom->appendChild($dom->importNode(dom_import_simplexml($other), true));
        } elseif ($other instanceof \DOMDocument) {
            $dom = $other;
        } else {
            $dom = new \DOMDocument('1.0');
            $dom->load($other);
        }

        $ret = $this->schemaIsXml() ? $dom->schemaValidateSource($this->schema) : $dom->schemaValidate($this->schema);
        if (!$ret) {
            $this->errors = libxml_get_errors();
        }

        return $ret;
    }

    /**
     * Return the XML errors as additional failure description
     *
     * @param  \DomDocument|\SimpleXMLElement|string $other  (not used)
     * @return string
     */
    protected function additionalFailureDescription($other)
    {
        $desc = '';

        foreach ($this->errors as $error) {
            $desc .= " - " . trim($error->message) . "\n";
        }

        return $desc;
    }

    /**
     * Returns the description of the failure
     *
     * The beginning of failure messages is "Failed asserting that" in most
     * cases. This method should return the second part of that sentence.
     *
     * To provide additional failure information additionalFailureDescription
     * can be used.
     *
     * @param  mixed  $other Evaluated value or object.
     * @return string
     */
    protected function failureDescription($other)
    {
        if ($other instanceof \SimpleXMLElement) {
            $xml = $other->asXML();
        } elseif ($other instanceof \DOMDocument) {
            $xml = $other->saveXML();
        } else {
            $xml = $other;
        }

        return $xml . ' ' . $this->toString();
    }

    /**
     * Returns a string representation of the constraint.
     *
     * @return string
     */
    public function toString()
    {
        return "validates against XSD schema" . ($this->schemaIsXml() ? '' : " '" . basename($this->schema) . "'");
    }
}
