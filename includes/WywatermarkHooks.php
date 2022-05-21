<?php
/*
 * WywatermarkHooks
*/

use MediaWiki\MediaWikiServices;

class WywatermarkHooks {
	
	public static function onUploadForm_initial($uploadFormObj) {
	    global $wgWywatermarkCat,$wgWywatermarkText;//传入文字水印文本参数
        //wfMessage( '加水印' )->text()用i18n语言
        //构造水印设置表单，加在“摘要”后面
        //图片水印位置、边距、不透明度
        $wmtext=Html::openElement( 'tr' ) .
        Html::openElement( 'td', ['class'=>'mw-label'] ) .
        Html::label( wfMessage( 'wywatermark-wmpos-label' )->text(), 'wmpos' ) .
        Html::closeElement( 'td' ) . Html::openElement( 'td' ) .
        Html::openElement( 'select',['id'=>'wmpos','name'=>'wmpos'] ).
        Html::rawElement( 'option',['value'=>'wmunuse'], wfMessage( 'wywatermark-wmunuse-option' )->text() ) .
        Html::rawElement( 'option',['value'=>'wmposnw'], wfMessage( 'wywatermark-wmposnw-option' )->text() ) .
        Html::rawElement( 'option',['value'=>'wmposwest'], wfMessage( 'wywatermark-wmposwest-option' )->text() ) .
        Html::rawElement( 'option',['value'=>'wmpossw'], wfMessage( 'wywatermark-wmpossw-option' )->text() ) .
        Html::rawElement( 'option',['value'=>'wmposnorth'], wfMessage( 'wywatermark-wmposnorth-option' )->text() ) .
        Html::rawElement( 'option',['value'=>'wmposmid'], wfMessage( 'wywatermark-wmposmid-option' )->text() ) .
        Html::rawElement( 'option',['value'=>'wmpossouth'], wfMessage( 'wywatermark-wmpossouth-option' )->text() ) .
        Html::rawElement( 'option',['value'=>'wmposne'], wfMessage( 'wywatermark-wmposne-option' )->text() ) .
        Html::rawElement( 'option',['value'=>'wmposeast'], wfMessage( 'wywatermark-wmposeast-option' )->text() ) .
        Html::rawElement( 'option',['value'=>'wmposse'], wfMessage( 'wywatermark-wmposse-option' )->text() ) .
        Html::closeElement( 'select' ).
        Html::label( wfMessage( 'wywatermark-wmborder-label' )->text(), 'wmborder' ) .
        Html::rawElement( 'input', ['id'=>'wmbordertext','name'=>'wmbordertext','size'=>'2','value'=>'20'] ) .
        Html::rawElement( 'span', [], wfMessage( 'wywatermark-wmborder-span' )->text() ) .
        Html::label( wfMessage( 'wywatermark-wmopacity-label' )->text(), 'wmopacity' ) .
        Html::rawElement( 'input', ['id'=>'wmopacitytext','name'=>'wmopacitytext','size'=>'2','value'=>'100'] ) .
        Html::rawElement( 'span', [], wfMessage( 'wywatermark-wmopacity-span' )->text() ) .
        Html::closeElement( 'td' ) . Html::closeElement( 'tr' );
        
        //图片水印文件名
        $wmtext.=Html::openElement( 'tr' ) .
        Html::openElement( 'td', ['class'=>'mw-label'] ) .
        Html::label( wfMessage( 'wywatermark-wmfile-label' )->text(), 'wmfile' ) .
        Html::closeElement( 'td' ) . Html::openElement( 'td' ) .
        Html::openElement( 'select',['id'=>'wmfile','name'=>'wmfile'] );
        /*从数据库获取[[分类:水印]]的所有文件名到选择器*/
        $lb = MediaWikiServices::getInstance()->getDBLoadBalancer();
        $dbr = $lb->getConnectionRef( DB_REPLICA );
        $res = $dbr->select(
            'categorylinks',
            'cl_from',
            "cl_to = '$wgWywatermarkCat'"
        );//获得所有分类水印的页面id
        //cl_sortkey是全部大写、下划线是空格，这样后面处理麻烦，再查页面标题
        $clfrom = array();
        foreach( $res as $row ) {
            array_push($clfrom,$row->cl_from);
        }
        if(count($clform)>0){//有id才查询否则报错
            $res = $dbr->select(
                'page',
                'page_title',
                'page_id in ('.implode(',',$clfrom).')'
            );//获得页面标题
            foreach( $res as $row ) {
                $wmtext.=Html::rawElement( 'option',['value'=>$row->page_title], $row->page_title );
            }
        }
        $wmtext.=Html::rawElement( 'input', ['id'=>'wmfilepc','name'=>'wmfilepc','size'=>'2','value'=>'100'] ) .
        Html::rawElement( 'span', [], wfMessage( 'wywatermark-wmfilepc-span' )->text() ) .
        Html::rawElement( 'a', ['href'=>"/index.php?title=Category:$wgWywatermarkCat",
        'title'=>"Category:$wgWywatermarkCat",'target'=>'_blank'],wfMessage( 'wywatermark-wmfilepcafter-span' )->text() ) .
        Html::closeElement( 'select' ).
        Html::closeElement( 'td' ) . Html::closeElement( 'tr' );
        
        //文字水印文本
        $wmtext.=Html::openElement( 'tr' ) .
        Html::openElement( 'td', ['class'=>'mw-label'] ) .
        Html::label( wfMessage( 'wywatermark-wmstr-label' )->text(), 'wmstr' ) .
        Html::closeElement( 'td' ) . Html::openElement( 'td' ) .
        Html::openElement( 'select',['id'=>'wmstr','name'=>'wmstr'] ).
        Html::rawElement( 'option',['value'=>'wmstrunuse'], wfMessage( 'wywatermark-wmstrunuse-option' )->text() );
        foreach ($wgWywatermarkText as $value){//遍历参数输入的文本数组
            $wmtext.=Html::rawElement( 'option',['value'=>$value], $value );
        }
        $wmtext.=Html::rawElement( 'option',['value'=>'wmstrusername'], wfMessage( 'wywatermark-wmstrusername-option' )->text() ) .
        Html::rawElement( 'option',['value'=>'wmstrinput'], wfMessage( 'wywatermark-wmstrinput-option' )->text() ) .
        Html::closeElement( 'select' ).
        Html::rawElement( 'input', ['id'=>'wmstrinputtext','name'=>'wmstrinputtext','value'=>''] ) .
        Html::closeElement( 'td' ) . Html::closeElement( 'tr' );
        //没有找到前端显示用户名的函数，暂时显示“上传者用户名”
        
        //文字水印样式配置（字体大小相对图片宽度百分比,透明度,旋转角度,文本间距相对文本宽度百分比）
        $wmtext.=Html::openElement( 'tr' ) .
        Html::openElement( 'td', ['class'=>'mw-label'] ) .
        Html::label( wfMessage( 'wywatermark-wmstrstyle-label' )->text(), 'wmstrstyle' ) .
        Html::closeElement( 'td' ) . Html::openElement( 'td' ) .
        Html::rawElement( 'span', [], wfMessage( 'wywatermark-wmfontsizepc-span' )->text() ) .
        Html::rawElement( 'input', ['id'=>'wmfontsizepc','name'=>'wmfontsizepc','size'=>'2','value'=>'3'] ) .
        Html::rawElement( 'span', [], wfMessage( 'wywatermark-wmfillalpha-span' )->text() ) .
        Html::rawElement( 'input', ['id'=>'wmfillalpha','name'=>'wmfillalpha','size'=>'2','value'=>'0.08'] ) .
        Html::rawElement( 'span', [], wfMessage( 'wywatermark-wmrotate-span' )->text() ) .
        Html::rawElement( 'input', ['id'=>'wmrotate','name'=>'wmrotate','size'=>'2','value'=>'-45'] ) .
        Html::rawElement( 'span', [], wfMessage( 'wywatermark-wmstrwidthpc-span' )->text() ) .
        Html::rawElement( 'input', ['id'=>'wmstrwidthpc','name'=>'wmstrwidthpc','size'=>'2','value'=>'120'] ) .
        Html::rawElement( 'span', [], '%' ) .
        Html::closeElement( 'td' ) . Html::closeElement( 'tr' );
        
        //添加html到摘要后面
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
		//wfDebugLog( 'Wywatermark', '收到提交的参数：位置：'.$wmpos.' );
		
		//读入图片文件，这里用url加相对目录位置方式找到图片绝对路径，可能通过FileBackend之类找图片位置才合理，无办法了
		$filepath = dirname(dirname(dirname(dirname(__FILE__)))).$image->getLocalFile()->url;//文件位置
		
		$img = new Imagick($filepath);
        $imageWH = $img->getImageGeometry();//array(width,height)
		
		//图片水印处理
		//不是不加图片水印、边距是正数、不透明度是0至100则加水印
		if ( $wmpos!='wmunuse' && $wmborder>=0 && $wmopacity>=0 && $wmopacity<=100) {
    		$wmfile = $wgRequest->getVal( 'wmfile' );
    		$wmfilepc = $wgRequest->getVal( 'wmfilepc' );
		    //水印文件
		    $wmfileurl = MediaWikiServices::getInstance()->getRepoGroup()->findFile( $wmfile )->url ;
    		$wmpath = dirname(dirname(dirname(dirname(__FILE__)))).$wmfileurl;
    		$wm = new Imagick($wmpath);
    		$ori_wmWH = $wm->getImageGeometry();
    		$wm->scaleImage($ori_wmWH['width']*$wmfilepc/100,$ori_wmWH['height']*$wmfilepc/100);
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
    		//获取样式：字体大小相对图片百分比，透明度，旋转角度，边距相对文字区域边长百分比
    		$fontsizepc=$wgRequest->getVal('wmfontsizepc');
    		$fillalpha=$wgRequest->getVal('wmfillalpha');
    		$rotate=$wgRequest->getVal('wmrotate');
    		$strwidthpc=$wgRequest->getVal('wmstrwidthpc');
    		if($fillalpha<0||$fillalpha>1){//透明度超出范围设为默认值0.08
		        $fillalpha=0.08;
		    }
            $draw = new ImagickDraw();
            $draw->setFillColor('white');
            $draw->setFont(dirname(dirname(__FILE__)).'/resources/font/SourceHanSansCN-Regular.ttf');//思源黑体常规
            $fontsize=$imageWH['width']*$fontsizepc/100;//按宽度百分比设置字体大小
            $draw->setFontSize($fontsize);
            $draw->setFillAlpha($fillalpha);
            $strwidth=$fontsize*mb_strlen($wmstrtext)*$strwidthpc/100;//按字体文本宽度百分比计算间距
            //计算起点，先分正负求旋转位置，再确定起点
            if($rotate>=0){
                $rotate=$rotate%360;
            }else{
                $rotate=360-abs($rotate)%360;
            }
            if($rotate>=0 && $rotate<90){
                $x0=$imageWH['width']/20;
                $y0=$imageWH['width']/20;
            }elseif($rotate>=90 && $rotate<180){
                $x0=$fontsize*mb_strlen($wmstrtext);
                $y0=$imageWH['width']/20;
            }elseif($rotate>=180 && $rotate<270){
                $x0=$fontsize*mb_strlen($wmstrtext);
                $y0=$fontsize*mb_strlen($wmstrtext);
            }else{
                $x0=$imageWH['width']/20;
                $y0=$fontsize*mb_strlen($wmstrtext);
            }
            //循环加文字水印，x从宽度1/20处开始，y从等于文本宽度的高度开始
            for($x=$x0;$x<$imageWH['width'];$x+=$strwidth){
                for($y=$y0;$y<$imageWH['height'];$y+=$strwidth){
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
