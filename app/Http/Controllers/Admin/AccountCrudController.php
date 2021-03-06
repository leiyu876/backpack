<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AccountRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class AccountCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class AccountCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Account::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/account');
        CRUD::setEntityNameStrings('account', 'accounts');
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

        $this->crud->removeColumn('deal_indicator');

        /**
         * Show relationship name
         */
        $this->crud->modifyColumn('sic_id', [
            'label' => "SIC", // Table column heading
            'type' => "select",
            'name' => 'sic_id', // the column that contains the ID of that connected entity;
            'entity' => 'sic', // the method that defines the relationship in your Model
            'attribute' => "description", // foreign key attribute that is shown to user
            'model' => 'App\Models\Sic' // foreign key model
        ]);

        $this->crud->modifyColumn('owners', [
            'name'  => 'owners',
            'type'     => 'closure',
            'function' => function($acount) {
                $owners = json_decode($acount->owners);
                $v = '';
                foreach ($owners as $owner) {
                    $v = $v.'<p>'.$owner->title.' '.$owner->owner_name.', '.$owner->date_of_birth.'</p>';
                }

                return $v;
            }
        ]);
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(AccountRequest::class);

        CRUD::setFromDb(); // fields

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
         */

        $this->crud->removeField('deal_indicator');

        $this->crud->modifyField('sic_id', [
            'label'     => "SIC Code",
            'type'      => 'select',
            'name'      => 'sic_id', 
            'model'     => "App\Models\Sic", 
            'attribute' => 'code', 
            'options'   => (function ($query) {
                return $query->orderBy('code', 'ASC')->get();
            }), 
        ]);

        $this->crud->modifyField('owners', [
            'name'  => 'owners',
            'label' => 'Owners',
            'type'  => 'repeatable',
            'fields' => [
                [
                    'name'    => 'owner_name',
                    'type'    => 'text',
                    'label'   => 'Owner Name',
                    'wrapper' => ['class' => 'form-group col-md-4'],
                ],
                [
                    'name'    => 'title',
                    'type'    => 'text',
                    'label'   => 'Title',
                    'wrapper' => ['class' => 'form-group col-md-4'],
                ],
                [
                    'name'    => 'date_of_birth',
                    'type'    => 'date_picker',
                    'label'   => 'Date of Birth',
                    'wrapper' => ['class' => 'form-group col-md-4'],
                ],
            ],

            // optional
            'new_item_label'  => 'Add Owner', // customize the text of the button
            'init_rows' => 1, // number of empty rows to be initialized, by default 1
            'min_rows' => 1, // minimum rows allowed, when reached the "delete" buttons will be hidden
            // 'max_rows' => 2, // maximum rows allowed, when reached the "new item" button will be hidden
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

    protected function setupShowOperation()
    {
        $this->crud->setFromDb();

        $this->crud->modifyColumn('owners', [
            'name'  => 'owners',
            'type'     => 'closure',
            'function' => function($acount) {
                $owners = json_decode($acount->owners);
                $v = '';
                foreach ($owners as $owner) {
                    $v = $v.'<p>'.$owner->title.' '.$owner->owner_name.', '.$owner->date_of_birth.'</p>';
                }

                return $v;
            }
        ]);

        $this->crud->removeColumn('deal_indicator');
    }
}
