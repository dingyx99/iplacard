<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 分配席位步骤视图
 * @author Kaijia Feng <fengkaijia@gmail.com>
 * @copyright 2013 Kaijia Feng
 * @license Dual-licensed proprietary
 * @link http://iplacard.com/
 * @package iPlacard
 * @since 2.0
 */
?><script>
	$('.selectpicker').selectpicker({
		iconBase: 'fa',
		tickIcon: 'fa-check'
	});

	jQuery(function($){
		$('#do_assign').hide();
	});
	
	function open_seat() {
		$('.nav-menu li').removeClass('active');
		$('#seat_tab').addClass('active');
		$('#seat_add').show();
		$('#seat_now').hide();
		$('#pre_assign').hide();
		$('#do_assign').show();
	}
	
	function assign_seat(id) {
		var last_id = $('input[name=assign_id]').val();
		$('input[name=assign_id]').val(id);
		$('#seat_name').html($('#seat-' + id).children().eq(1).html());
		$('#seat_committee').html($('#seat-' + id).children().eq(2).html());
		$('#seat_level').html($('#seat-' + id).children().eq(4).html());
		
		$('#seat-' + id).children().eq(5).html('');
		$('#seat-' + last_id).children().eq(5).html('<a style="cursor: pointer;"  onclick="assign_seat(' + last_id + ');"><?php echo icon('check-square-o', false);?>分配</a>');
		
		$('.selectpicker').selectpicker('refresh');
	}
	
	function add_seat(id) {
		$('select[name="recommended[]"]').append($('<option data-subtext="' + $('#seat-' + id).children().eq(2).html() + '"></option>').val($('#seat-' + id).children().eq(0).html()).html($('#seat-' + id).children().eq(1).html()));
		$('<input>').attr('type','hidden').attr('name', 'seat_open[]').val(id).appendTo('#seat_form');
		
		$('#seat-' + id).children().eq(6).html('<a style="cursor: pointer;" onclick="remove_seat(' + id + ');"><?php echo icon('minus-square', false);?>移除</a>');
		
		$('.selectpicker').selectpicker('refresh');
	}
	
	function remove_seat(id) {
		$('select[name="recommended[]"]').find('[value=' + id + ']').remove();
		$('input[value=' + id + ']').remove();
		
		$('#seat-' + id).children().eq(6).html('<a style="cursor: pointer;" onclick="add_seat(' + id + ');"><?php echo icon('plus-square', false);?>开放</a>');
		
		$('.selectpicker').selectpicker('refresh');
	}
</script>

<h3 id="admission_operation">分配席位</h3>

<?php if($global_admin && $interview && $interview['interviewer'] != uid()) { ?><span class="label label-warning">注意</span> 您正使用全局席位分配权限分配席位，该权限允许您向所有代表分配席位，请谨慎使用。<?php } ?>

<?php
if($interview)
{
	if($interview['status'] == 'completed' && !$score_level) { ?><p>代表于 <em><?php echo date('Y-m-d H:i', $interview['finish_time']);?></em> 通过面试，面试得分为 <strong><?php echo round($interview['score'], 2);?></strong>，满分为 <?php echo $score_total;?> 分。</p><?php }
	elseif($interview['status'] == 'completed' && $score_level) { ?><p>代表于 <em><?php echo date('Y-m-d H:i', $interview['finish_time']);?></em> 通过面试，面试得分为 <strong><?php echo round($interview['score'], 2);?></strong>，此成绩大约位于前 <?php echo $score_level;?>%，满分为 <?php echo $score_total;?> 分。</p><?php }
	elseif($interview['status'] == 'exempted') { ?><p>代表于 <em><?php echo date('Y-m-d H:i', $interview['assign_time']);?></em> 免试通过面试。</p><?php }
	
	if(isset($current_interview) && $current_interview['id'] != $interview['id'])
	{
		if($interview['status'] == 'failed') { ?><p>代表于 <em><?php echo date('Y-m-d H:i', $current_interview['finish_time']);?></em> 完成终试，但未能通过，终试得分为 <strong><?php echo round($current_interview['score'], 2);?></strong>。</p><?php }
		else { ?><p>代表于 <em><?php echo date('Y-m-d H:i', $current_interview['finish_time']);?></em> 通过终试，终试得分为 <strong><?php echo round($current_interview['score'], 2);?></strong>。</p><?php }
	}
}
?>

<div id="pre_assign">
	<?php
	if(!$mode == 'select')
	{
		if(!$assigned)
			echo '<p>现在您可以为代表分配席位选择。</p>';
		else
			echo "<p>您已经为代表开放了 {$selectability_count} 个席位。在申请锁定之前，您仍可以追加分配更多席位。</p>";
	}
	else
	{
		if(!$assigned)
			echo '<p>现在您可以为代表分配席位。</p>';
		else
			echo "<p>您已经为代表分配了席位。在申请锁定之前，您仍可以应代表要求调整分配的席位。</p>";
	}
	?>
	<p><a class="btn btn-primary" href="#seat" data-toggle="tab" onclick="open_seat();"><?php echo icon('th-list');?>分配席位</a></p>
</div>

<div id="do_assign">
	<?php
	echo form_open_multipart("delegate/operation/assign_seat/{$delegate['id']}", array('id' => 'seat_form'));
		if($mode == 'select')
		{ ?><p>下方选择框包含所有将要添加的席位列表，选定某一席位将会设置此席位为推荐席位。设置完成后点击提交分配将向代表开放选择框中所有席位。</p>
		
		<div class="form-group <?php if(form_has_error('recommended')) echo 'has-error';?>">
			<?php echo form_label('分配席位', 'recommended', array('class' => 'control-label'));?>
			<div>
				<?php echo form_dropdown_multiselect('recommended[]', array(), array(), false, array(), array(), array(), array(), 'selectpicker flags-16', 'data-selected-text-format="count" data-width="100%" title="选择推荐席位"');
				if(form_has_error('recommended'))
					echo form_error('recommended');
				?>
			</div>
		</div>
	
		<?php }	else
		{
			echo form_hidden('assign_id', $assigned ? $old_id : NULL); ?><p>请在表格中选择席位，点击席位右侧分配后将会显示席位信息。</p>
		<div class="form-group">
			<?php echo form_label('席位名称', 'seat_name', array('class' => 'control-label'));?>
			<div id="seat_name" class="well well-sm flags-16">
				&nbsp;
			</div>
		</div>
		
		<div class="form-group">
			<?php echo form_label('委员会', 'seat_committee', array('class' => 'control-label'));?>
			<div id="seat_committee" class="well well-sm">
				&nbsp;
			</div>
		</div>
		
		<div class="form-group">
			<?php echo form_label('等级', 'seat_level', array('class' => 'control-label'));?>
			<div id="seat_level" class="well well-sm">
				&nbsp;
			</div>
		</div><?php } ?>
	
		<?php echo form_button(array(
			'name' => 'submit',
			'content' => icon('check').'提交分配',
			'type' => 'submit',
			'class' => 'btn btn-primary',
			'onclick' => 'loader(this);'
		));?>
		
	<?php echo form_close(); ?>
</div>

<hr />