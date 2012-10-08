<?php
/*
 * This file was delivered to you as part of the Yucca package.
 *
 * (c) Rémi JANOT <r.janot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yucca\Bundle\YuccaBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateModelCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('yucca:generate-models')
            ->setDescription('Generates Models')
            ->addArgument('path', InputArgument::REQUIRED, 'Path into wich put models');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $mapping = $this->getContainer()->getParameter('yucca.mapping');
        foreach($mapping as $modelClassName=>$modelInformation) {
            $toAdd = $this->createFileContent($modelClassName, $modelInformation);
            $this->addToModel($input->getArgument('path'), $modelClassName, $toAdd);
        }
    }

    protected function addToModel($path, $modelClassName, array $toAdd) {
        //Check if we have something to add
        if(empty($toAdd['properties']) && empty($toAdd['methods'])) {
            return;
        }

        //Get File Path
        $realPath = array(
            rtrim($path,DIRECTORY_SEPARATOR),
            trim(str_replace('\\',DIRECTORY_SEPARATOR,$modelClassName),DIRECTORY_SEPARATOR),
        );
        $realPath = implode('/',$realPath).'.php';

        //Concatenate Properties and methods
        $properties = implode("\n\n",$toAdd['properties']);
        $methods = implode("\n\n",$toAdd['methods']);

        //If file exists, update
        if(file_exists($realPath)) {
            $fileContent = file_get_contents($realPath);
            $oldCode = substr($fileContent, 0, strrpos($fileContent, '}'));
            $data = <<<EOT
$oldCode

$properties

$methods
}

EOT;

        } else {
            $namespace = substr($modelClassName,0,strrpos($modelClassName,'\\'));
            $className = substr($modelClassName,strrpos($modelClassName,'\\')+1);
            $data = <<<EOT
<?php
namespace $namespace;
/**
 *
 */
class $className extends \\Yucca\\Model\\ModelAbstract {

$properties

$methods
}

EOT;
        }
        if(false === is_dir(dirname($realPath))) {
            mkdir(dirname($realPath),0775,true);
        }
        if(false === is_writable(dirname($realPath))) {
            throw new \RuntimeException(dirname($realPath).' is not writable');
        }
        if(file_exists($realPath) && false === is_writable($realPath)) {
            throw new \RuntimeException($realPath.' is not writable');
        }
        file_put_contents($realPath,$data);
    }

    /**
     * @param $modelClassName
     * @param $modelInformation
     * @return array
     */
    protected function createFileContent($modelClassName, $modelInformation) {
        //Collect all fields from sources
        $fields = array();
        $sources = $this->getContainer()->getParameter('yucca.sources');
        foreach($modelInformation['sources'] as $sourceName) {
            $fields = array_merge($fields, $sources[$sourceName]['default_params']['fields']);
        }

        if(isset($modelInformation['properties']) && is_array($modelInformation['properties'])) {
            foreach($modelInformation['properties'] as $propertyName => $propertyMapping) {
                if(isset($propertyMapping['field']) && isset($fields[$propertyMapping['field']])) {
                    $fields[$propertyName] = $fields[$propertyMapping['field']];
                    unset($fields[$propertyMapping['field']]);
                }
            }
        }

        //Fill in properties and methods
        $propertiesToAdd = array();
        $methodsToAdd = array();
        foreach($fields as $fieldName=>$fieldInformation) {
            $tmp = $this->generatePropertyCode($modelClassName, $fieldName, $this->extractFieldType($fieldInformation));
            if($tmp) {
                $propertiesToAdd[] = $tmp;
            }

            $tmp = $this->generatePropertyGetter($modelClassName, $fieldName, $this->extractFieldType($fieldInformation));
            if($tmp) {
                $methodsToAdd[] = $tmp;
            }

            $tmp = $this->generatePropertySetter($modelClassName, $fieldName, $this->extractFieldType($fieldInformation));
            if($tmp) {
                $methodsToAdd[] = $tmp;
            }
        }

        return array(
            'properties' => $propertiesToAdd,
            'methods' => $methodsToAdd,
        );
    }

    /**
     * @param $fieldInformation
     * @return string
     */
    protected function extractFieldType($fieldInformation) {
        $type = 'mixed';
        if(isset($fieldInformation['type']) && in_array($fieldInformation['type'], array('date','datetime'))) {
            $type = '\DateTime';
        } elseif(isset($fieldInformation['type']) && 'object' === $fieldInformation['type']) {
            $type = '\\'.$fieldInformation['class_name'];
        }

        return $type;
    }

    /**
     * @param $string
     * @param bool $ucFirst
     * @return string
     */
    protected function underscoreToCamelcase($string, $ucFirst) {

        $parts = explode('_', $string);
        $parts = $parts ? array_map('strtolower', $parts) : array($string);
        $parts = $parts ? array_map('ucfirst', $parts) : array($string);
        $parts[0] = $ucFirst ? ucfirst($parts[0]) : lcfirst($parts[0]);
        return implode('', $parts);
    }

    /**
     * @param $className
     * @param $fieldName
     * @param $type
     * @return string
     */
    protected function generatePropertyCode($className,$fieldName,$type) {
        $propertyName = $this->underscoreToCamelcase($fieldName, false);

        try {
            $class = new  \ReflectionClass($className);
            $class->getProperty($propertyName);
            return '';
        } catch(\ReflectionException $exception) {
            return <<<EOT
    /**
     * @var $type
     */
    protected \$$propertyName;
EOT;
        }
    }

    /**
     * @param $className
     * @param $fieldName
     * @param $type
     * @return string
     */
    protected function generatePropertyGetter($className,$fieldName,$type) {
        $propertyName = $this->underscoreToCamelcase($fieldName, false);
        $getterName = 'get'.$this->underscoreToCamelcase($fieldName, true);
        try {
            $class = new  \ReflectionClass($className);
            $class->getMethod($getterName);
            return '';
        } catch(\ReflectionException $exception) {
            return <<<EOT
    /**
     * @return $type
     */
    public function $getterName() {
        return \$this->$propertyName;
    }
EOT;
        }
    }

    /**
     * @param $className
     * @param $fieldName
     * @param $type
     * @return string
     */
    protected function generatePropertySetter($className,$fieldName,$type) {
        $propertyName = $this->underscoreToCamelcase($fieldName, false);
        $setterName = 'set'.$this->underscoreToCamelcase($fieldName, true);
        $typeHinting = (('mixed'===$type) ? '' : $type.' ');

        try {
            $class = new  \ReflectionClass($className);
            $class->getMethod($setterName);
            return '';
        } catch(\ReflectionException $exception) {
            return <<<EOT
    /**
     * @param $typeHinting\$$propertyName
     * @return \\$className
     */
    public function $setterName($typeHinting\$$propertyName) {
        \$this->$propertyName = \$$propertyName;
        return \$this;
    }
EOT;
        }
    }
}
