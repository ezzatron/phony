<?php

namespace Phony\Test;

class MockGeneratorCustomMethodInvocableObject
implements \Eloquent\Phony\Mock\Mock
{
    public function methodA()
    {
        $argumentCount = \func_num_args();
        $arguments = [];

        for ($i = 0; $i < $argumentCount; ++$i) {
            $arguments[] = \func_get_arg($i);
        }

        if (!$this->_handle) {
            $result = \call_user_func_array(
                [$this, 'parent::' . 'methodA'],
                $arguments
            );

            return $result;
        }

        $result = $this->_handle->spy(__FUNCTION__)->invokeWith(
            new \Eloquent\Phony\Call\Arguments($arguments)
        );

        return $result;
    }

    private static $_uncallableMethods = [];
    private static $_traitMethods = [];
    private static $_customMethods = [];
    private static $_staticHandle;
    private $_handle;
}
