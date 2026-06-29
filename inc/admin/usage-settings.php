<?php
/**
 * 使用说明设置
 *
 * @package wuchaiwp
 */

class Wuchaiwp_Usage_Settings {

    public function __construct() {
        if (!current_user_can('administrator')) {
            return;
        }
    }

    public function render_usage_page() {
        ?>
        <div class="wrap wuchaiwp-admin">
            <div class="wuchaiwp-header">
                <h1>使用说明</h1>
                <p class="description">了解如何使用 wuchaiwp 主题的各项功能</p>
            </div>


          <div class="usage-section support-section">
                    <h2>🛠️ 技术支持</h2>
                    <p>我们提供专业的技术支持服务，涵盖以下领域：</p>
                    <ul>
                        <li><strong>主题支持：</strong> wuchaiwp 主题的安装、配置和定制开发</li>
                        <li><strong>插件支持：</strong> 相关插件的使用和问题排查</li>
                        <li><strong>服务器环境配置：</strong> LAMP/LNMP 环境搭建与优化</li>
                        <li><strong>网站迁移：</strong> 网站数据迁移和服务器部署</li>
                    </ul>
                    <p>如有任何技术问题或需求，请访问：<a href="http://wuchai.net/" target="_blank" rel="noopener noreferrer">http://wuchai.net/</a></p>
                </div>



            <div class="usage-content">
                <div class="usage-section">
                    <h2>📝 快速开始</h2>
                    <ol>
                        <li>在「主题设置」->「首页设置」中配置Hero区域</li>
                        <li>在「外观设置」中自定义网站的颜色和字体</li>
                        <li>使用「样式预设」快速切换网站主题</li>
                        <li>在「版权说明」中设置网站版权信息</li>
                    </ol>
                </div>

                <div class="usage-section">
                    <h2>🏠 首页设置</h2>
                    <p>首页设置允许您配置Hero区域的显示内容：</p>
                    <ul>
                        <li><strong>Hero标题：</strong>首页顶部显示的主标题</li>
                        <li><strong>Hero描述：</strong>标题下方的描述文字</li>
                        <li><strong>背景图片：</strong>Hero区域的背景图片</li>
                        <li><strong>搜索框：</strong>自定义搜索框占位文本</li>
                    </ul>
                </div>

                <div class="usage-section">
                    <h2>🎨 外观设置</h2>
                    <p>外观设置允许您自定义网站的视觉风格：</p>
                    <ul>
                        <li><strong>页面尺寸：</strong>容器宽度、内容宽度、圆角半径、阴影强度</li>
                        <li><strong>字体设置：</strong>字体族、字体大小、行高</li>
                        <li><strong>颜色设置：</strong>主题色、次要色、背景色、文字色、链接色</li>
                    </ul>
                </div>

                <div class="usage-section">
                    <h2>🎭 样式预设</h2>
                    <p>样式预设提供了快速切换网站外观的方式：</p>
                    <ul>
                        <li><strong>保存预设：</strong>在外观设置中点击"保存为预设"</li>
                        <li><strong>应用预设：</strong>点击预设卡片上的"应用"按钮</li>
                        <li><strong>删除预设：</strong>点击预设卡片上的"删除"按钮</li>
                        <li><strong>内置预设：</strong>默认主题、深色主题、暖色主题</li>
                    </ul>
                </div>

                <div class="usage-section">
                    <h2>📦 自定义文章类型</h2>
                    <p>自定义文章类型允许您创建除默认文章和页面之外的内容类型：</p>
                    <ul>
                        <li><strong>创建文章类型：</strong>在「自定义文章类型」->「文章类型列表」中点击"创建新文章类型"</li>
                        <li><strong>基本信息：</strong>填写名称、标识（英文小写字母和连字符）、描述和选择图标</li>
                        <li><strong>功能支持：</strong>选择文章类型支持的功能（标题、编辑器、缩略图等）</li>
                        <li><strong>分类与标签：</strong>可以启用独立分类和标签功能，或关联默认分类法</li>
                        <li><strong>设置选项：</strong>设置是否公开、启用归档、显示管理界面等</li>
                        <li><strong>菜单位置：</strong>设置在后台菜单中的显示位置，数值越小越靠前</li>
                        <li><strong>编辑与删除：</strong>在文章类型列表中可以编辑或移到回收站</li>
                    </ul>
                    <h3>📌 使用技巧</h3>
                    <ul>
                        <li><strong>独立分类法：</strong>启用后会为该文章类型创建配套的独立分类和标签，不影响默认文章的分类标签</li>
                        <li><strong>菜单位置：</strong>推荐设置为1（最顶部）或5（媒体库上方）以便快速访问</li>
                        <li><strong>功能支持：</strong>根据需求选择必要的功能，不需要的功能可以取消勾选以简化编辑界面</li>
                        <li><strong>首页显示：</strong>在「多区域首页设置」中可以选择显示哪些文章类型的内容</li>
                    </ul>
                </div>
            </div>
        </div>
        <?php
    }
}

// 初始化
new Wuchaiwp_Usage_Settings();

/**
 * 渲染使用说明页面（供 Wuchaiwp_Settings 类调用）
 */
function wuchaiwp_render_usage_page() {
    $settings = new Wuchaiwp_Usage_Settings();
    $settings->render_usage_page();
}