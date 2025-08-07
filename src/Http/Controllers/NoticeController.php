<?php

namespace DagaSmart\Notice\Http\Controllers;

use DagaSmart\BizAdmin\Renderers\Form;
use DagaSmart\BizAdmin\Renderers\Page;
use DagaSmart\BizAdmin\Services\AdminService;
use DagaSmart\Notice\NoticeServiceProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use DagaSmart\BizAdmin\Renderers\Tpl;
use DagaSmart\BizAdmin\Renderers\Mapping;
use DagaSmart\Notice\Services\NoticeService;
use DagaSmart\BizAdmin\Renderers\TextControl;
use DagaSmart\BizAdmin\Renderers\TableColumn;
use DagaSmart\BizAdmin\Renderers\FormControl;
use DagaSmart\BizAdmin\Renderers\RadiosControl;
use DagaSmart\BizAdmin\Renderers\NumberControl;
use DagaSmart\BizAdmin\Renderers\SwitchControl;
use DagaSmart\BizAdmin\Renderers\SchemaPopOver;
use DagaSmart\BizAdmin\Renderers\SelectControl;
use DagaSmart\BizAdmin\Renderers\RichTextControl;
use DagaSmart\BizAdmin\Controllers\AdminController;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property AdminService|NoticeService $service
 */
class NoticeController extends AdminController
{
    protected string $serviceName = NoticeService::class;

    public function list(): Page
    {
        $crud = $this->baseCRUD()
            ->quickSaveItemApi(admin_url('notice_quick_edit'))
            ->headerToolbar([
                $this->createButton(true, 'lg'),
                ...$this->baseHeaderToolBar(),
            ])
            ->filter(
                $this->baseFilter()->body([
                    TextControl::make()->name('title')->label($this->trans('title'))->size('md'),
                    SelectControl::make()
                        ->name('type')
                        ->label($this->trans('type'))
                        ->size('md')
                        ->options($this->service->getType()),
                    SelectControl::make()
                        ->name('state')
                        ->label($this->trans('state'))
                        ->size('md')
                        ->options($this->service->getState()),
                ])
            )
            ->columns([
                TableColumn::make()->name('id')->label('ID')->sortable(true),
                TableColumn::make()
                    ->name('title')
                    ->label($this->trans('title'))
                    ->type('tpl')
                    ->tpl('${title | truncate: 24}')
                    ->popOver(
                        SchemaPopOver::make()->trigger('hover')->body(Tpl::make()->tpl('${title}'))
                    ),
                TableColumn::make()
                    ->name('type')
                    ->label($this->trans('type'))
                    ->type('mapping')
                    ->map($this->typeMapping()),
                TableColumn::make()->name('weight')->label($this->trans('weight'))->sortable(true)->quickEdit(
                    NumberControl::make()->displayMode('enhance')->set('saveImmediately', true)
                ),
                TableColumn::make()
                    ->name('state')
                    ->label($this->trans('state'))
                    ->quickEdit(
                        SwitchControl::make()->mode('inline')
                            ->onText($this->service->getState($this->service->getState(1)))
                            ->offText($this->service->getState($this->service->getState(0)))
                            ->set('saveImmediately', true)
                    ),
                TableColumn::make()
                    ->name('created_at')
                    ->label(__('admin.created_at'))
                    ->type('datetime')
                    ->sortable(true),
                TableColumn::make()
                    ->name('updated_at')
                    ->label(__('admin.updated_at'))
                    ->type('datetime')
                    ->sortable(true),
                $this->rowActions(true, 'lg')->set('width', 200),
            ]);

        return $this->baseList($crud);
    }

    public function form(): Form
    {
        return $this->baseForm()->data([
            'type'   => 'NOTICE',
            'weight' => 0,
            'state'  => 1,
        ])->body([
            TextControl::make()->name('title')->label($this->trans('title'))->required(true)->maxLength(255),
            RadiosControl::make()->name('type')->label($this->trans('type'))->options($this->service->getType()),
            NumberControl::make()
                ->name('weight')
                ->label($this->trans('weight'))
                ->displayMode('enhance')
                ->required(true),
            amis()->SwitchControl('is_global', $this->trans('is_global'))
                ->onText($this->service->getState(1))
                ->offText($this->service->getState(0)),
            amis()->SwitchControl('state',$this->trans('state'))
                ->onText($this->service->getState(1))
                ->offText($this->service->getState(0)),
            RichTextControl::make()->name('content')->label($this->trans('content'))->required(),
        ]);
    }

    public function detail($id): Form
    {
        $staticText = fn($name, $label) => TextControl::make()->static(true)->name($name)->label($label);

        return $this->baseDetail($id)->body([
            $staticText('id', 'ID'),
            $staticText('title', $this->trans('title')),
            FormControl::make()->static(true)->label($this->trans('type'))->body(
                Mapping::make()->name('type')->map($this->typeMapping())
            ),
            $staticText('weight', $this->trans('weight')),
            FormControl::make()->label($this->trans('state'))->body(
                Mapping::make()->name('state')->map([
                    1 => $this->label($this->service->getState(1), 'success'),
                    0 => $this->label($this->service->getState(0), 'warning'),
                ])
            ),
            FormControl::make()->label($this->trans('content'))->body(Tpl::make()->tpl('${content | raw}')),
            $staticText('created_at', __('admin.created_at')),
            $staticText('updated_at', __('admin.updated_at')),
        ]);
    }

    /**
     * 快速编辑
     *
     * @param Request $request
     *
     * @return JsonResponse|JsonResource
     */
    public function quickEdit(Request $request): JsonResponse|JsonResource
    {
        $primaryKey = $request->input('id');
        $data       = $request->only(['state', 'weight']);

        $result = $this->service->update($primaryKey, $data);

        return $this->autoResponse($result, __('admin.save'));
    }

    private function typeMapping(): array
    {
        return [
            'NOTICE'       => $this->label($this->service->getType('NOTICE'), 'primary'),
            'ANNOUNCEMENT' => $this->label($this->service->getType('ANNOUNCEMENT'), 'success'),
        ];
    }

    private function label($value, $type): string
    {
        return "<span class='label label-{$type}'>{$value}</span>";
    }

    private function trans($key): array|string|null
    {
        return NoticeServiceProvider::trans('notice.' . $key);
    }
}
