<?php

namespace DagaSmart\Notice\Services;

use Illuminate\Support\Arr;
use DagaSmart\Notice\Models\Notice as Model;
use DagaSmart\Notice\NoticeServiceProvider;
use DagaSmart\BizAdmin\Services\AdminService;

class NoticeService extends AdminService
{
    protected string $modelName = Model::class;

    public function getType($type = null)
    {
        $types = [
            Model::TYPE_NOTICE       => NoticeServiceProvider::trans('notice.notice'),
            Model::TYPE_ANNOUNCEMENT => NoticeServiceProvider::trans('notice.announcement'),
        ];

        return $type ? Arr::get($types, $type) : $types;
    }

    public function getState($state = null)
    {
        $states = [
            Model::STATE_SHOW => NoticeServiceProvider::trans('notice.show'),
            Model::STATE_HIDE => NoticeServiceProvider::trans('notice.hide'),
        ];

        return is_numeric($state) ? Arr::get($states, $state) : $states;
    }

    public function list()
    {
        $title = request()->title;
        $type  = request()->type;
        $state = request()->state;

        $query = $this->query()
            ->when($title, fn($q) => $q->where('title', 'like', "%{$title}%"))
            ->when($type, fn($q) => $q->where('type', $type))
            ->when($state, fn($q) => $q->where('state', $state))
            ->orderByDesc('updated_at');

        $items = (clone $query)->paginate(request()->input('perPage', 20))->items();
        $total = (clone $query)->count();

        return compact('items', 'total');
    }
}
