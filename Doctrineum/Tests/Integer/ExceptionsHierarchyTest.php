<?php
namespace Doctrineum\Tests\Integer;

use Doctrineum\Scalar\ScalarEnumInterface;
use Granam\Tests\Exceptions\Tools\AbstractExceptionsHierarchyTest;

class ExceptionsHierarchyTest extends AbstractExceptionsHierarchyTest
{
    protected function getTestedNamespace()
    {
        return $this->getRootNamespace();
    }

    protected function getRootNamespace()
    {
        return str_replace('\Tests', '', __NAMESPACE__);
    }

    protected function getExternalRootNamespaces()
    {
        $externalRootReflection = new \ReflectionClass(ScalarEnumInterface::class);

        return [$externalRootReflection->getNamespaceName()];
    }

}