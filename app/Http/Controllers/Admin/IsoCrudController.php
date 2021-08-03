<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\IsoRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class IsoCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class IsoCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Iso::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/iso');
        CRUD::setEntityNameStrings('iso', 'isos');
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

        $this->crud->modifyColumn('emails', [
            'name'  => 'emails',
            'type'     => 'closure',
            'function' => function($iso) {
                $emails = json_decode($iso->emails);
                
                $v = '';
                foreach ($emails as $item) {
                    $v = $v.'<p>'.$item->email.'</p>';
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
        CRUD::setValidation(IsoRequest::class);

        CRUD::setFromDb(); // fields

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
         */

        $this->crud->modifyField('emails', [
            'name'  => 'emails',
            'label' => 'Emails',
            'type'  => 'repeatable',
            'fields' => [
                [
                    'name'    => 'email',
                    'type'    => 'text',
                    'label'   => 'Email',
                    'wrapper' => ['class' => 'form-group col-md-4'],
                ],
            ],

            // optional
            'new_item_label'  => 'Add Email', // customize the text of the button
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
        CRUD::setFromDb();

        $this->crud->modifyColumn('emails', [
            'name'  => 'emails',
            'type'     => 'closure',
            'function' => function($iso) {
                $emails = json_decode($iso->emails);
                $emails_array = collect($emails)->pluck('email')->toArray();
                return implode(', ', $emails_array);
            }
        ]);
    }
}
