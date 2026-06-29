// 主题设置页面脚本
jQuery(document).ready(function($) {
    // 保存预设
    window.wuchaiwpSavePreset = function() {
        var presetName = prompt('请输入预设名称：');
        if (!presetName) return;
        
        $.ajax({
            url: wuchaiwp_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'wuchaiwp_save_preset',
                nonce: wuchaiwp_ajax.nonce,
                preset_name: presetName
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    window.location.href = '?page=wuchaiwp-style-presets';
                } else {
                    alert('保存失败');
                }
            }
        });
    };

    // 应用预设
    $(document).on('click', '.load-preset', function() {
        var presetKey = $(this).data('preset-key');
        
        $.ajax({
            url: wuchaiwp_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'wuchaiwp_load_preset',
                nonce: wuchaiwp_ajax.nonce,
                preset_key: presetKey
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    window.location.href = '?page=wuchaiwp-appearance-settings';
                } else {
                    alert('应用失败');
                }
            }
        });
    });

    // 删除预设
    $(document).on('click', '.delete-preset', function() {
        var presetKey = $(this).data('preset-key');
        if (!confirm('确定要删除这个预设吗？')) return;
        
        $.ajax({
            url: wuchaiwp_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'wuchaiwp_delete_preset',
                nonce: wuchaiwp_ajax.nonce,
                preset_key: presetKey
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert('删除失败');
                }
            }
        });
    });

    // 恢复默认
    window.wuchaiwpResetDefault = function() {
        if (!confirm('确定要恢复默认设置吗？')) return;
        
        $.ajax({
            url: wuchaiwp_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'wuchaiwp_reset_default',
                nonce: wuchaiwp_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert('操作失败');
                }
            }
        });
    };

    // 复制CSS代码
    window.wuchaiwpCopyCSS = function() {
        var code = document.getElementById('wuchaiwp-css-code').textContent;
        navigator.clipboard.writeText(code).then(function() {
            alert('代码已复制到剪贴板');
        }).catch(function() {
            alert('复制失败，请手动复制');
        });
    };
});