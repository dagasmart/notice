<?php

namespace DagaSmart\Notice\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use DagaSmart\BizAdmin\Models\BaseModel as Model;

class Notice extends Model
{
    use SoftDeletes;

    protected $table = 'notices';

    /** @var string 通知 */
    const string TYPE_NOTICE = 'NOTICE';
    /** @var string 公告 */
    const string TYPE_ANNOUNCEMENT = 'ANNOUNCEMENT';

    /** @ 显示 */
    const int STATE_SHOW = 1;
    /** @ 隐藏 */
    const int STATE_HIDE = 0;
}
