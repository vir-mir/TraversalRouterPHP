<?php
/**
 * Created by PhpStorm.
 * User: vir-mir
 * Date: 13.03.15
 * Time: 19:16
 */

namespace TraversalRouterPHP;

use TraversalRouterPHP\Exception\Traversal as ExceptionTraversal;

class Traversal {

    private $namespace;

    private $template;

    /**
     * @var \TraversalRouterPHP\Action
     */
    private $action;


    /**
     * @var $this;
     */
    private $parent;



    public function __construct($namespace, $template, $parent = null) {
        $this->namespace = $namespace;
        $this->template = $template;
        $this->parent = $parent;
    }


    /**
     * @param string[] $urls
     * @return $this
     * @throws ExceptionTraversal
     */
    public function match($urls) {

        $url = reset($urls) ? ucfirst(reset($urls)) : false;
        $object = $this->namespace . '\\' . $url . '\\Route.php';

        if (class_exists($object)) {
            array_shift($urls);
            $object = new $object($this->namespace, $this->template, $this);
            if (count($urls) > 1) {
                $url = $urls[1];
                if (!class_exists($this->namespace . '\\' . $url . '\\Route.php')) {
                    array_shift($urls);
                }
            }
            return $object->match($urls);
        }

        $this->logic($urls);

        return $this;
    }

    /**
     * @return \TraversalRouterPHP\Action
     */
    public function getAction() {
        return $this->action;
    }

    /**
     * @return null|$this
     */
    public function getParent() {
        return $this->parent;
    }


    /**
     * @param string[] $urls
     * @param \TraversalRouterPHP\Action $object
     * @return bool
     */
    protected function isAction($urls, $object) {
        if (count($urls) > 2 || count($urls) < 1) {
            return false;
        }
        $action = 'action' . ucfirst(array_shift($urls));

        if (method_exists($object, $action)) {
            return true;
        }

        return false;
    }

    /**
     * @param string[] $urls
     * @throws ExceptionTraversal
     */
    protected function logic($urls) {
        $object = __NAMESPACE__ . '\\Logic.php';

        if (class_exists($object)) {
            $action = new $object($urls);
        } else {
            throw new ExceptionTraversal('undefined action for the traversal "' . get_class($this) . '".');
        }
    }

} 