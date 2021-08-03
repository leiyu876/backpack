<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\DealRequest;
use App\Models\Iso;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class DealCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class DealCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Deal::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/deal');
        CRUD::setEntityNameStrings('deal', 'deals');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::setFromDb(); // columns

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
         */

        /**
         * Show relationship name
         */
        $this->crud->modifyColumn('account_id', [
            'label' => "Account", // Table column heading
            'type' => "select",
            'name' => 'account_id', // the column that contains the ID of that connected entity;
            'entity' => 'account', // the method that defines the relationship in your Model
            'attribute' => "business_name", // foreign key attribute that is shown to user
            'model' => 'App\Models\Account' // foreign key model
        ]);

        $this->crud->modifyColumn('iso_id', [
            'label' => "ISO", 
            'type' => "select",
            'name' => 'iso_id', 
            'entity' => 'iso', 
            'attribute' => "business_name", 
            'model' => 'App\Models\Iso' // foreign key model
        ]);

        $this->crud->modifyColumn('sales_stage', [
            'type' => "select_from_array",
            'name' => 'sales_stage', 
            'options' => config('constants.sales_stages'),
        ]);

        /**
         * Filters
         */
        $this->crud->addFilter([ 
                'name'  => 'iso_id',
                'type'  => 'dropdown',
                'label' => 'ISO'
            ],
            Iso::pluck('business_name', 'id')->toArray(),
            function($value) {
                $this->crud->addClause('where', 'iso_id', $value); 
            }
        );

        $this->crud->addFilter([ 
                'name'  => 'sales_stage',
                'type'  => 'dropdown',
                'label' => 'Sales Stage'
            ],
            config('constants.sales_stages'),
            function($value) { 
                $this->crud->addClause('where', 'sales_stage', $value); 
            }
        );

        $this->crud->addFilter([
                'type'  => 'date_range',
                'name'  => 'submission_date',
                'label' => 'Submission Date'
            ],
            false,
            function ($value) {
                $dates = json_decode($value);
                $this->crud->addClause('where', 'submission_date', '>=', $dates->from);
                $this->crud->addClause('where', 'submission_date', '<=', $dates->to . ' 23:59:59');
            }
        );
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(DealRequest::class);

        CRUD::setFromDb(); // fields

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
         */

        $this->crud->modifyField('account_id', [
            'label'     => "Account",
            'type'      => 'select',
            'name'      => 'account_id', // the db column for the foreign key
            'entity'    => 'account', 
            'model'     => "App\Models\Account", // related model
            'attribute' => 'business_name', // foreign key attribute that is shown to user
            'options'   => (function ($query) {
                return $query->orderBy('business_name', 'ASC')->get();
            }), //  you can use this to filter the results show in the select
        ]);

        $this->crud->modifyField('iso_id', [
            'label'     => "iso_id",
            'type'      => 'select',
            'name'      => 'iso_id', 
            'entity'    => 'iso', 
            'model'     => "App\Models\Iso", 
            'attribute' => 'business_name', 
            'options'   => (function ($query) {
                return $query->orderBy('business_name', 'ASC')->get();
            }), 
        ]);

        $this->crud->modifyField('sales_stage', [
            'name'        => 'sales_stage',
            'label'       => "Sales Stage",
            'type'        => 'select_from_array',
            'options'     => config('constants.sales_stages'),
            'allows_null' => false,
            'default'     => 'new',
        ]);
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
