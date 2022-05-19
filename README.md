# mediawiki-Wywatermark
mediawiki-Wywatermark是为Mediawiki开发的一个插件。此插件在上传文件页面添加水印配置表单，并在上传完成对原图添加水印。

由于作者对代码只懂皮毛，代码并不科学，它只是实现功能，慎重用于正式环境，欢迎帮忙修正、优化。

## 插件功能与原理
* 使用[UploadForm:initial](https://www.mediawiki.org/wiki/Manual:Hooks/UploadForm:initial)在上传文件表单添加水印配置选项；
* 使用[UploadComplete](https://www.mediawiki.org/wiki/Manual:Hooks/UploadComplete)在上传文件完成时对文件使用php的imagick对图片添加水印。

## 环境要求
* 系统需要安装imagemagick；
* php需要安装imagick、mbstring。

## 使用方法
* 下载插件放在`extensions`文件夹内，重命名此插件文件夹名为`Wywatermark`；
* 在`LocalSettings.php`加入`wfLoadExtension( 'Wywatermark' );`；
* 【可选】设置参数`$wgWywatermarkText=["水印文本1","水印文本2"];`（数组）。不设置则`文字水印：`下拉选择框只有“不适用文字水印，上传者用户名，右侧输入文本”；设置此参数则可添加预设文本到下拉选择框选项中；
* 完成。

## 更新日志
* 20220519：初步实现功能。

## 可能存在的问题
* 插件名称“Wywatermark”仅为了避免和其他插件名或参数名冲突，暂时个人觉得存在的问题就是获取图片路径的方法，还有就是配置选项较多每张图片上传均需要选一次可能不够方便。尚不明确有无插件冲突或其他问题。

## 相关链接
* 字体：[思源黑体](https://github.com/adobe-fonts/source-han-sans)
* [示例水印图片来源](https://pixabay.com/zh/vectors/button-yes-no-red-green-icon-32259/)
