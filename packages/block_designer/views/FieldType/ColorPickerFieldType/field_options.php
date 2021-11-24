<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="content-field-options">
    <div class="form-group">
        <h4><?php echo t('General Options'); ?></h4>

        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="control-label">
                        <input type="checkbox" name="fields[{{id}}][flat]" value="1" id="fields[{{id}}][flat]" {{#xif " this.flat == '1' " }}checked="checked"{{/xif}}>
                        <?php echo t('Always show up at full size (flat)'); ?>
                        <br/><small><?php echo t('Positioned as an inline-block element'); ?></small>
                    </label>
                </div>

                <div class="form-group">
                    <label class="control-label">
                        <input type="checkbox" name="fields[{{id}}][show_alpha]" value="1" id="fields[{{id}}][show_alpha]" {{#xif " this.show_alpha == '1' " }}checked="checked"{{/xif}}>
                        <?php echo t('Allow alpha transparency selection'); ?>
                    </label>
                </div>

                <div class="form-group">
                    <label class="control-label">
                        <input type="checkbox" name="fields[{{id}}][disabled]" value="1" id="fields[{{id}}][disabled]" {{#xif " this.disabled == '1' " }}checked="checked"{{/xif}}>
                        <?php echo t('Automatically disable'); ?>
                    </label>
                </div>

                <div class="form-group">
                    <label class="control-label">
                        <input type="checkbox" name="fields[{{id}}][show_input]" value="1" id="fields[{{id}}][show_input]" {{#xif " this.show_input == '1' " }}checked="checked"{{/xif}}>
                        <?php echo t('Show input'); ?>
                        <br/><small><?php echo t('Uses an input box with the color value'); ?></small>
                    </label>
                </div>

                <div class="form-group">
                    <label class="control-label">
                        <input type="checkbox" name="fields[{{id}}][allow_empty]" value="1" id="fields[{{id}}][allow_empty]" {{#xif " this.allow_empty == '1' " }}checked="checked"{{/xif}}>
                        <?php echo t('Allow empty'); ?>
                        <br/><small><?php echo t("Value can be cleared by clicking a '%s' button", t('Clear Color Selection')); ?></small>
                    </label>
                </div>

                <div class="form-group">
                    <label class="control-label">
                        <input type="checkbox" name="fields[{{id}}][show_buttons]" value="1" id="fields[{{id}}][show_buttons]" {{#xif " this.show_buttons == '1' " }}checked="checked"{{/xif}}>
                        <?php echo t('Show buttons'); ?>
                        <br/><small><?php echo t('If there are no buttons, the behavior will be to fire the `change` event (and update the original input) when the picker is closed.'); ?></small>
                    </label>
                </div>

                <div class="form-group">
                    <label class="control-label">
                        <input type="checkbox" name="fields[{{id}}][clickout_fires_change]" value="1" id="fields[{{id}}][clickout_fires_change]" {{#xif " this.clickout_fires_change == '1' " }}checked="checked"{{/xif}}>
                        <?php echo t('Clickout fires change'); ?>
                        <br/><small><?php echo t('When clicking outside of the colorpicker, you can force it to fire a change event rather than having it revert the change.'); ?></small>
                    </label>
                </div>

                <div class="form-group">
                    <label class="control-label">
                        <input type="checkbox" name="fields[{{id}}][show_initial]" value="1" id="fields[{{id}}][show_initial]" {{#xif " this.show_initial == '1' " }}checked="checked"{{/xif}}>
                        <?php echo t('Show the color that was initially set when opening'); ?>
                    </label>
                </div>
            </div>

            <div class="col-sm-6 side-xs-xs">
                <div class="form-group">
                    <label for="fields[{{id}}][fallback_value]" class="control-label">
                        <?php echo t('Fallback value'); ?>
                    </label>
                    <input type="text"
                           name="fields[{{id}}][fallback_value]"
                           id="fields[{{id}}][fallback_value]"
                           size="3"
                           value="{{fallback_value}}"
                           class="form-control"/>
                </div>

                <div class="form-group">
                    <label for="fields[{{id}}][preferred_format]" class="control-label">
                        <?php echo t('Preferred Format'); ?>
                    </label>

                    <select name="fields[{{id}}][preferred_format]" class="form-control size" id="fields[{{id}}][preferred_format]">
                        {{#select preferred_format}}
                        <option value="false"><?php echo t('None (Depends on input)'); ?></option>
                        <option value="hex"><?php echo t('Hex'); ?></option>
                        <option value="hex3"><?php echo t('Hex (3 Characters If Possible)'); ?></option>
                        <option value="hsl"><?php echo t('Hsl'); ?></option>
                        <option value="rgb"><?php echo t('Rgb'); ?></option>
                        <option value="name"><?php echo t('Name (Falls back to hex)'); ?></option>
                        {{/select}}
                    </select>
                </div>

                <div class="form-group">
                    <label for="fields[{{id}}][clear_text]" class="control-label">
                        <?php echo t("'%s' text", t('Clear Color Selection')); ?>
                    </label>
                    <input type="text" name="fields[{{id}}][clear_text]" placeholder="Clear Color Selection" value="{{clear_text}}" id="fields[{{id}}][clear_text]" class="form-control"/>
                </div>

                <div class="form-group">
                    <label for="fields[{{id}}][no_color_selected_text]" class="control-label">
                        <?php echo t("'%s' text", t('No Color Selected')); ?>
                    </label>
                    <input type="text" name="fields[{{id}}][no_color_selected_text]" placeholder="No Color Selected" value="{{no_color_selected_text}}" id="fields[{{id}}][no_color_selected_text]" class="form-control"/>
                </div>

                <div class="form-group">
                    <label for="fields[{{id}}][choose_text]" class="control-label">
                        <?php echo t("'%s' text", t('Choose')); ?>
                    </label>
                    <input type="text" name="fields[{{id}}][choose_text]" placeholder="choose" value="{{choose_text}}" id="fields[{{id}}][choose_text]" class="form-control"/>
                </div>

                <div class="form-group">
                    <label for="fields[{{id}}][cancel_text]" class="control-label">
                        <?php echo t("'%s' text", t('Cancel')); ?>
                    </label>
                    <input type="text" name="fields[{{id}}][cancel_text]" placeholder="cancel" value="{{cancel_text}}" id="fields[{{id}}][cancel_text]" class="form-control"/>
                </div>

                <div class="form-group">
                    <label for="fields[{{id}}][theme]" class="control-label">
                        <?php echo t('Theme'); ?>
                        <small><?php echo t('a-z, dashes and underscore only'); ?></small>
                    </label>
                    <input type="text" data-validation-optional="true" data-validation="custom" data-validation-regexp="^([a-z]+)([a-z-_]+)$" name="fields[{{id}}][theme]" placeholder="sp-light" value="{{theme}}" id="fields[{{id}}][theme]" class="form-control"/>
                </div>
            </div>
        </div>

        <h4><?php echo t('Palette Options'); ?></h4>

        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="fields[{{id}}][toggle_palette_more_text]" class="control-label">
                        <?php echo t("Toggle Palette 'more' text"); ?>
                    </label>
                    <input type="text" name="fields[{{id}}][toggle_palette_more_text]" placeholder="more" value="{{toggle_palette_more_text}}" id="fields[{{id}}][toggle_palette_more_text]" class="form-control"/>
                </div>

                <div class="form-group">
                    <label for="fields[{{id}}][toggle_palette_less_text]" class="control-label">
                        <?php echo t("Toggle Palette 'less' text"); ?>
                    </label>
                    <input type="text" name="fields[{{id}}][toggle_palette_less_text]" placeholder="less" value="{{toggle_palette_less_text}}" id="fields[{{id}}][toggle_palette_less_text]" class="form-control"/>
                </div>

                <div class="form-group">
                    <label for="fields[{{id}}][max_selection_size]" class="control-label">
                        <?php echo t('Max Selection Size'); ?>
                        <small><?php echo t('How many elements are allowed in the selection palette at once'); ?></small>
                    </label>
                    <input type="text" name="fields[{{id}}][max_selection_size]" data-validation-optional="true" data-validation="number" data-validation-allowing="range[1;99],negative" placeholder="7" value="{{max_selection_size}}" id="fields[{{id}}][max_selection_size]" class="form-control"/>
                </div>
            </div>

            <div class="col-sm-6 side-xs-xs">
                <div class="form-group">
                    <label class="control-label">
                        <input type="checkbox" name="fields[{{id}}][toggle_palette_only]" value="1" id="fields[{{id}}][toggle_palette_only]" {{#xif " this.toggle_palette_only == '1' " }}checked="checked"{{/xif}}>
                        <?php echo t('Show a button to toggle the colorpicker next to the palette'); ?>
                    </label>
                </div>

                <div class="form-group">
                    <label class="control-label">
                        <input type="checkbox" class="show_palette" name="fields[{{id}}][show_palette]" value="1" id="fields[{{id}}][show_palette]" {{#xif " this.show_palette == '1' " }}checked="checked"{{/xif}}>
                        <?php echo t('Show a palette'); ?>
                        <br/><small><?php echo t('Positioned below the colorpicker to make it convenient for users to choose from frequently or recently used colors'); ?></small>
                    </label>
                </div>

                <div class="form-group">
                    <label class="control-label">
                        <input type="checkbox" name="fields[{{id}}][show_palette_only]" value="1" id="fields[{{id}}][show_palette_only]" {{#xif " this.show_palette_only == '1' " }}checked="checked"{{/xif}}>
                        <?php echo t('Show the palettes you specify, and nothing else'); ?>
                    </label>
                </div>

                <div class="form-group">
                    <label class="control-label">
                        <input type="checkbox" name="fields[{{id}}][hide_after_palette_select]" value="1" id="fields[{{id}}][hide_after_palette_select]" {{#xif " this.hide_after_palette_select == '1' " }}checked="checked"{{/xif}}>
                        <?php echo t('Automatically hide after a palette color is selected'); ?>
                    </label>
                </div>

                <div class="form-group">
                    <label class="control-label">
                        <input type="checkbox" name="fields[{{id}}][show_selection_palette]" value="1" id="fields[{{id}}][show_selection_palette]" {{#xif " this.show_selection_palette == '1' " }}checked="checked"{{/xif}}>
                        <?php echo t('Keep track of what has been selected by the user'); ?>
                        <br/><small><?php echo t("requires '%s'", t('Show a palette')); ?></small>
                    </label>
                </div>

                <div class="form-group">
                    <label class="control-label">
                        <input type="checkbox" name="fields[{{id}}][local_storage]" value="1" id="fields[{{id}}][local_storage]" {{#xif " this.local_storage == '1' " }}checked="checked"{{/xif}}>
                        <?php echo t("Save selection in browser's localStorage object"); ?>
                    </label>
                </div>
            </div>
        </div>

        <div class="pallet-rows-container hidden">
            <h4><?php echo t('Palette Rows'); ?></h4>

            <div class="alert alert-info">
                <p>
                    <?php echo t('You can enter Hex, Hsl, Rgb, Rgba (with alpha) or Named color values. See the list below for examples'); ?>
                </p>

                <table class="table">
                    <thead>
                    <tr>
                        <th><?php echo t('Type'); ?></th>
                        <th><?php echo t('Value'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><?php echo t('Hex'); ?></td>
                        <td><strong>#ececec</strong></td>
                    </tr>
                    <tr>
                        <td><?php echo t('Hsl'); ?></td>
                        <td><strong>hsl(0, 100%, 50%)</strong></td>
                    </tr>
                    <tr>
                        <td><?php echo t('Rgb'); ?></td>
                        <td><strong>rgb(255, 128, 0)</strong></td>
                    </tr>
                    <tr>
                        <td><?php echo t('Rgba'); ?></td>
                        <td><strong>rgba(0, 255, 0, 0.5)</strong></td>
                    </tr>
                    <tr>
                        <td><?php echo t('Named'); ?></td>
                        <td><strong>white</strong></td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="palette-rows">
                {{#if palette}}
                    {{#each palette}}
                    <div class="palette-row" data-attr-row="{{@index}}">
                        <div class="row">
                            {{#each this}}
                            <div class="palette-color">
                                <div class="col-sm-4 col-lg-3">
                                    <div class="form-group">
                                        <input type="text" name="fields[{{../../id}}][palette][{{@../index}}][]" value="{{this}}" class="form-control"/>

                                        <a href="#" class="btn-delete-color">
                                            <i class="fa fa-times"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            {{/each}}
                        </div>

                        <a href="#" class="btn btn-info btn-add-color"><?php echo t('Add Color'); ?></a>
                        <a href="#" class="btn btn-danger btn-delete-row"><?php echo t('Delete Row'); ?></a>
                    </div>
                    {{/each}}
                {{else}}
                    <div class="palette-row" data-attr-row="0">
                        <div class="row">
                            <div class="palette-color">
                                <div class="col-sm-4 col-lg-3">
                                    <div class="form-group">
                                        <input type="text" name="fields[{{id}}][palette][0][]" class="form-control" />

                                        <a href="#" class="btn-delete-color">
                                            <i class="fa fa-times"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <a href="#" class="btn btn-info btn-add-color"><?php echo t('Add Color'); ?></a>
                        <a href="#" class="btn btn-danger btn-delete-row"><?php echo t('Delete Row'); ?></a>
                    </div>
                {{/if}}

                <a href="#" class="btn btn-success btn-add-row"><?php echo t('Add Row'); ?></a>
            </div>
        </div>
    </div>
</div>