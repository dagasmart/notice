(function() {
    // 获取未读通知数量
    function updateNoticeCount() {
        fetch('/admin/notice/api/count')
            .then(response => response.json())
            .then(data => {
                if (data.status === 0) {
                    const badge = document.querySelector('.notice-badge .badge');
                    if (badge) {
                        badge.textContent = data.data.count;
                        badge.style.display = data.data.count > 0 ? 'block' : 'none';
                    }
                }
            });
    }

    // 页面加载时更新通知数量
    document.addEventListener('DOMContentLoaded', updateNoticeCount);

    // 每30秒更新一次
    setInterval(updateNoticeCount, 30000);
})();
