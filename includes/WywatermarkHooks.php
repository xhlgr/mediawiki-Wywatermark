<?php
/*
 * WywatermarkHooks
*/

class WywatermarkHooks {
	
	public static function onUploadForm_initial($uploadFormObj) {
	    global $wgWywatermarkText;//传入文字水印文本参数
        //wfMessage( '加水印' )->text()用i18n语言
		//构造水印设置表单，加在“摘要”后面
		//图片水印位置
        $wmtext=Html::openElement( 'tr' ) .
        Html::openElement( 'td', ['class'=>'mw-label'] ) .
        Html::label( wfMessage( 'wywatermark-wmpos-label' )->text(), 'wmpos' ) .
        Html::closeElement( 'td' ) . Html::openElement( 'td' ) .
        Html::openElement( 'select',['id'=>'wmpos','name'=>'wmpos'] ).
        Html::rawElement( 'option',['value'=>'wmunuse'], wfMessage( 'wywatermark-wmunuse-option' )->text() ) .
        Html::rawElement( 'option',['value'=>'wmposnw'], wfMessage( 'wywatermark-wmposnw-option' ) ) .
        Html::rawElement( 'option',['value'=>'wmposwest'], wfMessage( 'wywatermark-wmposwest-option' ) ) .
        Html::rawElement( 'option',['value'=>'wmpossw'], wfMessage( 'wywatermark-wmpossw-option' ) ) .
        Html::rawElement( 'option',['value'=>'wmposnorth'], wfMessage( 'wywatermark-wmposnorth-option' ) ) .
        Html::rawElement( 'option',['value'=>'wmposmid'], wfMessage( 'wywatermark-wmposmid-option' ) ) .
        Html::rawElement( 'option',['value'=>'wmpossouth'], wfMessage( 'wywatermark-wmpossouth-option' ) ) .
        Html::rawElement( 'option',['value'=>'wmposne'], wfMessage( 'wywatermark-wmposne-option' ) ) .
        Html::rawElement( 'option',['value'=>'wmposeast'], wfMessage( 'wywatermark-wmposeast-option' ) ) .
        Html::rawElement( 'option',['value'=>'wmposse'], wfMessage( 'wywatermark-wmposse-option' ) ) .
        Html::closeElement( 'select' ).
        Html::closeElement( 'td' ) . Html::closeElement( 'tr' );
        //图片水印边距
        $wmtext.=Html::openElement( 'tr' ) .
        Html::openElement( 'td', ['class'=>'mw-label'] ) .
        Html::label( wfMessage( 'wywatermark-wmborder-label' ), 'wmborder' ) .
        Html::closeElement( 'td' ) . Html::openElement( 'td' ) .
        Html::rawElement( 'input', ['id'=>'wmbordertext','name'=>'wmbordertext','value'=>'20'] ) .
        Html::rawElement( 'span', [], wfMessage( 'wywatermark-wmborder-span' ) ) .
        Html::closeElement( 'td' ) . Html::closeElement( 'tr' );
        //图片水印不透明度
        $wmtext.=Html::openElement( 'tr' ) .
        Html::openElement( 'td', ['class'=>'mw-label'] ) .
        Html::label( wfMessage( 'wywatermark-wmopacity-label' ), 'wmopacity' ) .
        Html::closeElement( 'td' ) . Html::openElement( 'td' ) .
        Html::rawElement( 'input', ['id'=>'wmopacitytext','name'=>'wmopacitytext','value'=>'100'] ) .
        Html::rawElement( 'span', [], wfMessage( 'wywatermark-wmopacity-span' ) ) .
        Html::closeElement( 'td' ) . Html::closeElement( 'tr' );
        //图片水印文件名
        $wmtext.=Html::openElement( 'tr' ) .
        Html::openElement( 'td', ['class'=>'mw-label'] ) .
        Html::label( wfMessage( 'wywatermark-wmfile-label' ), 'wmfile' ) .
        Html::closeElement( 'td' ) . Html::openElement( 'td' ) .
        Html::openElement( 'select',['id'=>'wmfile','name'=>'wmfile'] );
        // 获取水印图目录下所有文件名到选择器
        $files = array();
        $filedata = scandir(dirname(dirname(__FILE__))."/resources/watermark");
        foreach ($filedata as $value){
            if($value != '.' && $value != '..'){
                $wmtext.=Html::rawElement( 'option',['value'=>$value], $value );
            }
        }
        $wmtext.=Html::closeElement( 'select' ).
        Html::closeElement( 'td' ) . Html::closeElement( 'tr' );
        //文字水印文本
        $wmtext.=Html::openElement( 'tr' ) .
        Html::openElement( 'td', ['class'=>'mw-label'] ) .
        Html::label( wfMessage( 'wywatermark-wmstr-label' ), 'wmstr' ) .
        Html::closeElement( 'td' ) . Html::openElement( 'td' ) .
        Html::openElement( 'select',['id'=>'wmstr','name'=>'wmstr'] ).
        Html::rawElement( 'option',['value'=>'wmstrunuse'], wfMessage( 'wywatermark-wmstrunuse-option' ) );
        foreach ($wgWywatermarkText as &$value){//遍历参数输入的文本数组
            $wmtext.=Html::rawElement( 'option',['value'=>$value], $value );
        }
        $wmtext.=Html::rawElement( 'option',['value'=>'wmstrusername'], wfMessage( 'wywatermark-wmstrusername-option' ) ) .
        Html::rawElement( 'option',['value'=>'wmstrinput'], wfMessage( 'wywatermark-wmstrinput-option' ) ) .
        Html::closeElement( 'select' ).
        Html::rawElement( 'input', ['id'=>'wmstrinputtext','name'=>'wmstrinputtext','value'=>''] ) .
        Html::closeElement( 'td' ) . Html::closeElement( 'tr' );
        //没有找到前端显示用户名的函数，暂时显示“上传者用户名”
        //文字水印样式配置（字体大小相对图片宽度百分比,透明度,旋转角度,文本间距相对文本宽度百分比）
        $wmtext.=Html::openElement( 'tr' ) .
        Html::openElement( 'td', ['class'=>'mw-label'] ) .
        Html::label( wfMessage( 'wywatermark-wmstrstyle-label' ), 'wmstrstyle' ) .
        Html::closeElement( 'td' ) . Html::openElement( 'td' ) .
        Html::rawElement( 'input', ['id'=>'wmstrstyle','name'=>'wmstrstyle','value'=>'3,0.1,-45,120'] ) .
        Html::rawElement( 'span', [], wfMessage( 'wywatermark-wmstrstyle-span' ) ) .
        Html::closeElement( 'td' ) . Html::closeElement( 'tr' );
		//添加内容到摘要后面
        $uploadFormObj->uploadFormTextAfterSummary = $wmtext;
    }
    
	public static function onUploadComplete($image) {
	    global $wgRequest;
	    
	    if ( $image->getLocalFile() === null) {
			return true;//没有文件则结束
		}

		$wmpos = $wgRequest->getVal( 'wmpos' );
		$wmborder = $wgRequest->getVal( 'wmbordertext' );
		$wmopacity = $wgRequest->getVal( 'wmopacitytext' );
		$wmfile = $wgRequest->getVal( 'wmfile' );
		//wfDebugLog( 'Wywatermark', '收到提交的参数：位置：'.$wmpos.'，边距：'.$wmborder.'，不透明度：'.$wmopacity.'，不透明度：'.$wmfile );
		
		//读入图片文件，这里用url加相对目录位置方式找到图片绝对路径，可能通过FileBackend之类找图片位置才合理
		$filepath = dirname(dirname(dirname(dirname(__FILE__)))).$image->getLocalFile()->url;//文件位置
		
		$img = new Imagick($filepath);
        $imageWH = $img->getImageGeometry();//array(width,height)
		
		//图片水印处理
		//不是不加图片水印、边距是正数、不透明度是0至100则加水印
		if ( $wmpos!='wmunuse' && $wmborder>=0 && $wmopacity>=0 && $wmopacity<=100) {
		    //水印文件
    		$wmpath = dirname(dirname(__FILE__)).'/resources/watermark/'.$wmfile;
    		$wm = new Imagick($wmpath);
            $wmWH = $wm->getImageGeometry();
            
            if($imageWH['width']>$wmWH['width']+$wmborder && $imageWH['height']>$wmWH['height']+$wmborder){
                //图片宽高比水印宽高+边距大才加水印，其实不严谨
                $wm->evaluateImage(Imagick::EVALUATE_MULTIPLY, $wmopacity/100, Imagick::CHANNEL_ALPHA);//水印图设置不透明度
                //计算合入位置左上角坐标(x,y)
                $x=0;
                $y=0;
                switch($wmpos){
                    case 'wmposnw'://左上
                        $x=$x+$wmborder;
                        $y=$y+$wmborder;
                        break;
                    case 'wmposwest'://左中
                        $x=$x+$wmborder;
                        $y=$imageWH['height']/2-$wmWH['height']/2;
                        break;
                    case 'wmpossw'://左下
                        $x=$x+$wmborder;
                        $y=$imageWH['height']-$wmWH['height']-$wmborder;
                        break;
                    case 'wmposnorth'://中上
                        $x=$imageWH['width']/2-$wmWH['width']/2;
                        $y=$y+$wmborder;
                        break;
                    case 'wmposmid'://正中
                        $x=$imageWH['width']/2-$wmWH['width']/2;
                        $y=$imageWH['height']/2-$wmWH['height']/2;
                        break;
                    case 'wmpossouth'://中下
                        $x=$imageWH['width']/2-$wmWH['width']/2;
                        $y=$imageWH['height']-$wmWH['height']-$wmborder;
                        break;
                    case 'wmposne'://右上
                        $x=$imageWH['width']-$wmWH['width']-$wmborder;
                        $y=$y=$y+$wmborder;
                        break;
                    case 'wmposeast'://右中
                        $x=$imageWH['width']-$wmWH['width']-$wmborder;
                        $y=$imageWH['height']/2-$wmWH['height']/2;
                        break;
                    case 'wmposse'://右下
                        $x=$imageWH['width']-$wmWH['width']-$wmborder;
                        $y=$imageWH['height']-$wmWH['height']-$wmborder;
                        break;
                }
                $img->compositeImage($wm, imagick::COMPOSITE_OVER, $x, $y);//合入水印
                $wmimgflag=true;//标记有图片水印处理
            }
		}
		
		//文字水印处理
		$wmstr = $wgRequest->getVal( 'wmstr' );
		if($wmstr!='wmstrunuse'){//不是不加文字水印
		    $wmstrtext='';//水印文本
		    switch($wmstr){//文本内容
                case 'wmstrusername':
                    $wmstrtext=$image->getLocalFile()->getUser('text');
		            break;
                case 'wmstrinput':
                    $wmstrtext=$wgRequest->getVal('wmstrinputtext');
		            break;
		        default:
		            $wmstrtext=$wmstr;
		    }
    		//获取样式，格式：字体大小相对图片百分比，透明度，旋转角度，边距相对文字区域边长百分比
    		$wmstrstyle=$wgRequest->getVal('wmstrstyle');
    		if(substr_count($wmstrstyle,",")==3){//三个英文逗号则认为正常输入进行分割
    		    $stylearr=explode(",",$wmstrstyle);
    		    $fontsizepc=$stylearr[0];
    		    $fillalpha=$stylearr[1];
    		    if($fillalpha<0||$fillalpha>1){//透明度超出范围设为默认值0.1
    		        $fillalpha=0.1;
    		    }
    		    $rotate=$stylearr[2];
    		    $strwidthpc=$stylearr[3];
    		}else{//否则赋默认值
    		    $fontsizepc=3;
    		    $fillalpha=0.1;
    		    $rotate=-45;
    		    $strwidthpc=120;
    		}
            $draw = new ImagickDraw();
            $draw->setFillColor('white');
            $draw->setFont(dirname(dirname(__FILE__)).'/resources/font/SourceHanSansCN-Regular.ttf');//思源黑体常规
            $fontsize=$imageWH['width']*$fontsizepc/100;//按宽度百分比设置字体大小
            $draw->setFontSize($fontsize);
            $draw->setFillAlpha($fillalpha);
            $strwidth=$fontsize*mb_strlen($wmstrtext)*$strwidthpc/100;//按字文本宽度百分比计算间距
			//循环加文字水印
            for($x=$imageWH['width']/20;$x<$imageWH['width'];$x+=$strwidth){//从宽度1/20处开始
                for($y=$fontsize*mb_strlen($wmstrtext);$y<$imageWH['height'];$y+=$strwidth){//从等于文本宽度的高度开始
                    $img->annotateImage($draw, $x, $y, $rotate, $wmstrtext);
                }
            }
            $wmstrflag=true;//标记有文字水印处理
		}
        
        if(isset($wmimgflag)||isset($wmstrflag)){//有处理的才需要覆盖
            $img->writeImage($filepath);//写入覆盖
        }
        return true;
    }
}