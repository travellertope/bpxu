

	<?php if (empty($times)): ?>
		<div class="align-item-center pt-1">
			<h4 class="mb-0"><i class="lni lni-cross-circle text-danger"></i></h4>
			<p class="time-empty-info pt-0 mt-xs-20"><?php echo trans('schedule-not-available') ?></p>
		</div>
	<?php else: ?>

		<p class="pick-date fs-14 shadow-sm"><?php echo trans('book-session-time-for') ?> <span><?php echo my_date_show($date) ?></span></p>
		
		<div class="time_wrap pl-0 <?php if(count($times) > 20){echo "h-370";}else{ echo "h-auto"; } ?>">
		<?php foreach ($times as $time): ?>
			<?php

				$from_timezone = get_by_id($user_timezone,'time_zone')->name;
				$to_timezone = get_by_id($time_zone,'time_zone')->name;

				$time_val = date("H:i", strtotime($time['start'])).'-'.date("H:i", strtotime($time['end'])); 
				$start_time = date("H:i", strtotime($time['start']));
				$converted_start_time = convert_timezone($start_time,$from_timezone,$to_timezone);
				$end_time = date("H:i", strtotime($time['end']));
				$converted_end_time = convert_timezone($end_time,$from_timezone,$to_timezone);
				
				// check booking time slots duplication
				$check = check_time($time_val, $date, $session_id, $mentor_id);
				$session = $this->admin_model->get_by_id($session_id,'sessions');
				if(isset($company->time_format) && $company->time_format == 'HH'){
					$time_view = date("H:i", strtotime($converted_start_time)).'-'.date("H:i", strtotime($converted_end_time)); 
				}else{
					$time_view = date("h:i a", strtotime($converted_start_time)).'-'.date("h:i a", strtotime($converted_end_time)); 
				}
			?>

			<div class="time_group pt-1 pl-0 mt-2">
				<div class="btn-group w-100">

					<?php $slot_count = count_session_time_slot($session_id, $date, $time_val);?>
					<?php $remaining_slot  = $session->group_booking_slot - $slot_count;?>

				    <label data-val="<?php echo html_escape($time_view) ?>" data-id="<?php echo html_escape($time['id']) ?>" class="text-center btn-block btn-sm time_btn <?php if($session->enable_group_booking == 1 && $slot_count == $session->group_booking_slot){echo 'disabled';} ?> <?php if($session->enable_group_booking == 0 && $check == true){echo 'disabled';} ?>">
				      <input type="radio" class="time_inp" value="<?php echo html_escape($time_val) ?>" name="time" autocomplete="off"><i class="far fa-clock"></i> <?php echo html_escape($time_view)?><br>
				      
				      <?php if($session->enable_group_booking == 1): ?>
				       	<b>(<?php echo html_escape($remaining_slot); ?> <?php echo trans('left') ?>)</b>
				       <?php endif; ?>
				    </label>
				</div>
			</div>
			<?php //endif ?>

			<input type="hidden" name="time_slot_id" class="time_slot_id" value="0">
			<input type="hidden" name="convert_time_slot" class="convert_time_slot" value="">

		<?php endforeach ?>
		</div>
		
		
	<?php endif ?>
