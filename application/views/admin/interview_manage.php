<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 面试管理视图
 * @author Kaijia Feng <fengkaijia@gmail.com>
 * @copyright 2013 Kaijia Feng
 * @license Dual-licensed proprietary
 * @link http://iplacard.com/
 * @package iPlacard
 * @since 2.0
 */

$this->ui->html('header', '<link href="'.static_url(is_dev() ? 'static/css/bootstrap.datatables.css' : 'static/css/bootstrap.datatables.min.css').'" rel="stylesheet">');
$this->ui->html('header', '<script src="'.static_url(is_dev() ? 'static/js/jquery.datatables.js' : 'static/js/jquery.datatables.min.js').'"></script>');
$this->ui->html('header', '<script src="'.static_url(is_dev() ? 'static/js/locales/jquery.datatables.locale.js' : 'static/js/locales/jquery.datatables.locale.min.js').'"></script>');
$this->ui->html('header', '<script src="'.static_url(is_dev() ? 'static/js/bootstrap.datatables.js' : 'static/js/bootstrap.datatables.min.js').'"></script>');
$this->ui->html('header', '<script src="'.static_url(is_dev() ? 'static/js/jquery.shorten.js' : 'static/js/jquery.shorten.min.js').'"></script>');
$this->load->view('header');?>

<div class="page-header">
	<div class="row">
		<div class="col-md-<?php echo !empty($part) ? '8' : '12';?>">
			<h1><?php echo $title;?></h1>
		</div>
		<?php $this->ui->js('footer', 'nav_menu_top();
		$(window).resize(function($){
			nav_menu_top();
		});');
		if(!empty($part)) { ?><div class="col-md-4 menu-tabs">
			<ul class="nav nav-tabs nav-menu">
				<li<?php if($part == 'all') echo ' class="active"';?>><?php echo anchor('interview/manage?'.$param_tab['all'], '全部面试');?></li>
				<li<?php if($part == 'pending') echo ' class="active"';?>><?php echo anchor('interview/manage?'.$param_tab['pending'], '未完成面试');?></li>
				<li<?php if($part == 'finished') echo ' class="active"';?>><?php echo anchor('interview/manage?'.$param_tab['finished'], '已完成面试');?></li>
			</ul>
		</div><?php } ?>
	</div>
</div>

<div class="menu-pills"></div>

<table id="interview_list" class="table table-striped table-bordered table-hover table-responsive">
	<thead>
		<tr>
			<th>ID</th>
			<th>面试者</th>
			<th>面试官</th>
			<th>面试状态</th>
			<th>分配时间</th>
			<th>安排时间</th>
			<th>完成时间</th>
			<th>面试评分</th>
			<th>操作</th>
		</tr>
	</thead>
	
	<tbody>
		
	</tbody>
</table>

<?php
$read_more = '<span class="label label-default">展开详细评分</span>';
$read_less = icon('caret-left', false);
$interviewer_list = $show_interviewer ? '' : '2';
$ajax_url = base_url('interview/ajax/list?'.$param_uri);
$ajax_js = <<<EOT
$(document).ready(function() {
	$('#interview_list').dataTable( {
		"aoColumnDefs": [
			{ "bSortable": false, "aTargets": [ 0, 8 ] },
			{ "bVisible": false, "aTargets": [ {$interviewer_list} ] },
			{ "bVisible": false, "aTargets": [ 9, 10, 11 ] },
			{ "iDataSort": 9, "aTargets": [ 4 ] },
			{ "iDataSort": 10, "aTargets": [ 5 ] },
			{ "iDataSort": 11, "aTargets": [ 6 ] }
		],
		"bProcessing": true,
		"bAutoWidth": false,
		"sAjaxSource": '{$ajax_url}',
		"fnDrawCallback": function() {
			$('.contact_list').popover();
		
			$('.shorten').shorten({
				showChars: '0',
				moreText: '{$read_more}',
				lessText: '{$read_less}'
			});
		}
	} );
} );
EOT;
$this->ui->js('footer', $ajax_js);
$this->load->view('footer');?>