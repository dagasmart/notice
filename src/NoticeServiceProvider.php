<?php

namespace DagaSmart\Notice;

use DagaSmart\BizAdmin\Extend\ServiceProvider;
use DagaSmart\BizAdmin\Renderers\Form;
use DagaSmart\BizAdmin\Renderers\NumberControl;
use DagaSmart\BizAdmin\Renderers\SwitchControl;
use Exception;

class NoticeServiceProvider extends ServiceProvider
{
    protected $menu = [
        [
            'title'    => '通知公告',
            'url'      => '/notice',
            'url_type' => '1',
            'icon'     => 'material-symbols:format-list-bulleted',
        ],
    ];

    /**
     * @return void
     * @throws Exception
     */
    public function register(): void
    {
        parent::register();

        /**加载路由**/
        parent::registerRoutes(__DIR__.'/Http/routes.php');
        /**加载语言包**/
        if ($lang = parent::getLangPath()) {
            $this->loadTranslationsFrom($lang, $this->getCode());
        }
    }

    public function settingForm(): ?Form
    {
        return $this->baseSettingForm()->body([
            SwitchControl::make()
                ->name('enabled')
                ->label('启用通知')
                ->value(true),

            NumberControl::make()
                ->name('max_notices')
                ->label('最大通知数量')
                ->value(100)
                ->min(10)
                ->max(1000),

            SwitchControl::make()
                ->name('auto_delete')
                ->label('自动删除过期通知')
                ->value(false),

            NumberControl::make()
                ->name('expire_days')
                ->label('过期天数')
                ->value(30)
                ->min(1)
                ->max(365)
                ->visibleOn('${auto_delete}'),
        ]);
    }

}
