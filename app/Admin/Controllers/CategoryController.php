<?php

namespace App\Admin\Controllers;

use App\Models\Category;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class CategoryController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Category';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Category());

        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('types', __('Type'));
        $grid->column('count', __('Count'));
        $grid->column('order', __('Order'))->sortable();
        $grid->column('url', __('Url'))->display(function ($url) {
            return '<img src="'.$url.'" alt="Image" style="max-width: 200px; max-height: 200px;">';
        });
        $grid->column('createdDate', __('CreatedDate'))->display(function ($createdDate) {
            // Chuyển đổi miliseconds thành định dạng ngày tháng năm
            $formattedDate = date('d-m-Y', $createdDate / 1000);
            return $formattedDate;
        })->sortable()->style('text-align:center');

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Category::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('url', __('Url'));
        $show->field('types', __('Types'));
        $show->field('count', __('Count'));
        $show->field('order', __('Order'));
        $show->field('createdDate', __('CreatedDate'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Category());

        $form->text('name', __('Name'))->rules('required');
        $form->text('url', __('Url'))->rules('required');
        $form->text('types', __('Types'))->rules('required');
        $form->number('count', __('Count'))->default(0);
        $form->number('order', __('Order'))->default(0);
        $form->saving(function ($form) {
            $image = $form->model();

            // create
            if ($image->id === null) {
                // get last Id
                $lastId = $image->getLastId();
                //
                $image->id = $lastId->id + 1;
                $image->createdDate = round(microtime(true) * 1000);
            }
        });
        return $form;
    }
}
