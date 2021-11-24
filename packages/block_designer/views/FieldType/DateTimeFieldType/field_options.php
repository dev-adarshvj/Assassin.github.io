<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="content-field-options">
    <div class="form-group">
        <label for="fieldDateTimeDateFormat[{{id}}]" class="control-label">
            <?php echo t('Date format'); ?>
            <span class="required">*</span>

            <small><?php echo t("i.e. %s will generate %s - more info on <a href='%s' target='_blank'>%s</a>", '%A %d %B %Y', strftime('%A %d %B %Y', time()), 'http://php.net/manual/en/function.strftime.php#refsect1-function.strftime-parameters', 'http://php.net'); ?></small>
        </label>
        <input type="text" name="fields[{{id}}][date_format]" placeholder="%A %d %B %Y" data-validation="required" value="{{date_format}}" id="fieldDateTimeDateFormat[{{id}}]" class="form-control"/>
    </div>

    <h4>
        <?php echo t('Datetimepicker plugin config'); ?>
        <small><a href="http://eonasdan.github.io/bootstrap-datetimepicker/#options" target="_blank"><?php echo t('View datetimepicker options/defaults'); ?></a></small>
    </h4>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">
                    <input type="checkbox" name="fields[{{id}}][pick_date]" value="0" id="fieldDateTimePickDate[{{id}}]" {{#xif " this.pick_date == '0' " }}checked="checked"{{/xif}}>
                    <?php echo t('Hide the date picker'); ?>
                </label>
            </div>
            <div class="form-group">
                <label class="control-label">
                    <input type="checkbox" name="fields[{{id}}][pick_time]" value="0" id="fieldDateTimePickTime[{{id}}]" {{#xif " this.pick_time == '0' " }}checked="checked"{{/xif}}>
                    <?php echo t('Hide the time picker'); ?>
                </label>
            </div>
            <div class="form-group">
                <label class="control-label">
                    <input type="checkbox" name="fields[{{id}}][use_seconds]" value="0" id="fieldDateTimeUseSeconds[{{id}}]" {{#xif " this.use_seconds == '0' " }}checked="checked"{{/xif}}>
                    <?php echo t('Hide the seconds picker'); ?>
                </label>
            </div>
            <div class="form-group">
                <label class="control-label">
                    <input type="checkbox" name="fields[{{id}}][use_minutes]" value="0" id="fieldDateTimeUseMinutes[{{id}}]" {{#xif " this.use_minutes == '0' " }}checked="checked"{{/xif}}>
                    <?php echo t('Hide the minutes picker'); ?>
                </label>
            </div>
            <div class="form-group">
                <label class="control-label">
                    <input type="checkbox" name="fields[{{id}}][use_current]" value="0" id="fieldDateTimeUseCurrent[{{id}}]" {{#xif " this.use_current == '0' " }}checked="checked"{{/xif}}>
                    <?php echo t('Do not set the value to the current date/time'); ?>
                </label>
            </div>
            <div class="form-group">
                <label class="control-label">
                    <input type="checkbox" name="fields[{{id}}][show_today]" value="0" id="fieldDateTimeShowToday[{{id}}]" {{#xif " this.show_today == '0' " }}checked="checked"{{/xif}}>
                    <?php echo t('Do not show the today indicator'); ?>
                </label>
            </div>
            <div class="form-group">
                <label class="control-label">
                    <input type="checkbox" name="fields[{{id}}][side_by_side]" value="1" id="fieldDateTimeSideBySide[{{id}}]" {{#xif " this.side_by_side == '0' " }}checked="checked"{{/xif}}>
                    <?php echo t('Show the date and time picker side by side'); ?>
                </label>
            </div>
            <div class="form-group">
                <label class="control-label">
                    <input type="checkbox" name="fields[{{id}}][use_strict]" value="1" id="fieldDateTimeUseStrict[{{id}}]" {{#xif " this.use_strict == '1' " }}checked="checked"{{/xif}}>
                    <?php echo t("Use 'strict' when validating dates"); ?>
                </label>
            </div>
        </div>

        <div class="col-md-6 side-xs-xs side-sm-xs">
            <div class="form-group">
                <label for="fields[{{id}}][minute_stepping]" class="control-label"><?php echo t('Minute stepping'); ?></label>
                <input type="text" name="fields[{{id}}][minute_stepping]" placeholder="1" value="{{minute_stepping}}" id="fields[{{id}}][minute_stepping]" class="form-control"/>
            </div>
            <div class="form-group">
                <label for="fields[{{id}}][min_date]" class="control-label">
                    <?php echo t('Minimum date'); ?>
                    <small><?php echo t('i.e. %s', '10/25/1900'); ?></small>
                </label>
                <input type="text" name="fields[{{id}}][min_date]" value="{{min_date}}" id="fields[{{id}}][min_date]" class="form-control" data-validation-optional="true" data-validation="date" data-validation-format="mm/dd/yyyy"/>
            </div>
            <div class="form-group">
                <label for="fields[{{id}}][max_date]" class="control-label">
                    <?php echo t('Maximum date'); ?>
                    <small><?php echo t('Defaults to today +100 years'); ?></small>
                </label>
                <input type="text" name="fields[{{id}}][max_date]" value="{{max_date}}" id="fields[{{id}}][max_date]" class="form-control" data-validation-optional="true" data-validation="date" data-validation-format="mm/dd/yyyy"/>
            </div>
            <div class="form-group">
                <label for="fields[{{id}}][default_date]" class="control-label">
                    <?php echo t('Default date'); ?>
                </label>
                <input type="text" name="fields[{{id}}][default_date]" value="{{default_date}}" id="fields[{{id}}][default_date]" class="form-control" data-validation-optional="true" data-validation="date" data-validation-format="mm/dd/yyyy"/>
            </div>
        </div>
    </div>
</div>