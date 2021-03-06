<?php namespace Cartalyst\Sentry\Resources\Eloquent;

use Cartalyst\Sentry\Resources\ProviderInterface;
use Cartalyst\Sentry\Resources\ResourceNotFoundException;

class Provider implements ProviderInterface {

    /**
     * The Eloquent group model.
     *
     * @var string
     */
    protected $model = 'Cartalyst\Sentry\Groups\Eloquent\Resource';

    /**
     * resources tree
     * @var null
     */
    protected $resourcesTree = null;

    /**
     * Create a new Eloquent Group provider.
     *
     * @param  string  $model
     * @return void
     */
    public function __construct($model = null)
    {
        if (isset($model))
        {
            $this->model = $model;
        }
    }

    /**
     * Find the resource by ID.
     *
     * @param  int  $id
     * @return \Cartalyst\Sentry\Resources\ResourceInterface  $resource
     * @throws \Cartalyst\Sentry\Resources\ResourceNotFoundException
     */
    public function findById($id)
    {
        $model = $this->createModel();

        if ( ! $resource = $model->newQuery()->find($id))
        {
            throw new ResourceNotFoundException("A resource could not be found with ID [$id].");
        }

        return $resource;
    }

    /**
     * Find the resource by name.
     *
     * @param  string  $name
     * @return \Cartalyst\Sentry\Resources\ResourceInterface  $resource
     * @throws \Cartalyst\Sentry\Resources\ResourceNotFoundException
     */
    public function findByName($name)
    {
        $model = $this->createModel();

        if ( ! $resource = $model->newQuery()->where('name', '=', $name)->first())
        {
            throw new ResourceNotFoundException("A resource could not be found with the name [$name].");
        }

        return $resource;
    }

    /**
     * find by parent id
     * @param int $id
     * @return mixed
     */
    public function findByParent($id){
        $model = $this->createModel();

        if ( ! $resource = $model->newQuery()->where('parent_id', '=', $id)->first())
        {
            throw new ResourceNotFoundException("A resource could not be found with the parent_id [$id].");
        }

        return $resource;
    }

    /**
     * Find the resource by code
     * @param string $value
     * @return mixed
     * @throws \Cartalyst\Sentry\Resources\ResourceNotFoundException
     */
    public function findByValue($value){
        $model = $this->createModel();

        if ( ! $group = $model->newQuery()->where('value', '=', $value)->first())
        {
            throw new ResourceNotFoundException("A resource could not be found with the value [$value].");
        }

        return $group;
    }

    /**
     * Returns all resources.
     *
     * @return array  $groups
     */
    public function findAll()
    {
        $model = $this->createModel();

        return $model->newQuery()->get()->all();
    }


    /**
     * builds resources tree
     * @param $elements
     * @param int $parentId
     * @return array
     */
    protected function buildTree($elements, $parentId = 0) {
        $branch = array();

        foreach ($elements as $element) {
            if ($element->parent_id == $parentId) {
                $children = $this->buildTree($elements, $element->id);
                if ($children) {
                    $element->childrens = $children;
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }

    /**
     * builds resources tree
     * @param $elements
     * @param int $parentId
     * @return array
     */
    protected function buildFlatTree($elements, $parentId = 0) {
        $branch = array();

        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children = $this->buildFlatTree($elements, $element['id']);
                if ($children) {
                    $element['childrens'] = $children;
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }


    /**
     * Returns tree of resources
     * @param boolean flat - flats tree to array
     * @param int $parentId
     * @return null
     */
    public function getTree($flat=false, $parentId=0){
        $model = $this->createModel();
        $resources = $model->newQuery()->orderBy('parent_id', 'DESC')->get();

        if ($flat == false){
            return $this->buildTree($resources, $parentId);
        }else{
            return $this->buildFlatTree($resources->toArray(), $parentId);
        }
    }

    /**
     * Creates a resource.
     *
     * @param  array  $attributes
     * @return \Cartalyst\Sentry\Resources\ResourceInterface
     */
    public function create(array $attributes)
    {
        $resource = $this->createModel();
        $resource->fill($attributes);
        $resource->save();

        return $resource;
    }

    /**
     * Create a new instance of the model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createModel()
    {
        $class = '\\'.ltrim($this->model, '\\');

        return new $class;
    }

    /**
     * Sets a new model class name to be used at
     * runtime.
     *
     * @param  string  $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

}