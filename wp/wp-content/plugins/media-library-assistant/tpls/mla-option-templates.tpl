<!-- template="taxonomy-table" -->
        <tr valign="top">
		<td colspan="2" class="textleft">
          <table class="taxonomytable">
		  <thead>
		  <tr>
		    <th scope="col" class="mla-settings-taxonomy-th">
			[+Support+]
			</th>
		    <th scope="col" class="mla-settings-taxonomy-th">
			[+Inline Edit+]
			</th>
		    <th scope="col" class="mla-settings-taxonomy-th">
			[+Term Search+]
			</th>
		    <th scope="col" class="mla-settings-taxonomy-th">
			[+Checklist+]
			</th>
		    <th scope="col" class="mla-settings-taxonomy-th">
			[+Checked On Top+]
			</th>
		    <th scope="col" class="mla-settings-taxonomy-th">
			[+List Filter+]
			</th>
		    <th scope="col" class="textleft" style="padding: 0px 5px 0px 5px;">
			[+Taxonomy+]
			</th>
			</tr>
			</thead>
			<tbody>
[+taxonomy_rows+]
			</tbody>
          </table>
          <div style="font-size:8pt;padding-bottom:10px;">[+help+]</div>
        </td></tr>

<!-- template="taxonomy-row" -->
        <tr valign="top">
		<td style="text-align:center;">
            <input type="checkbox" name="tax_support[[+key+]]" id="tax_support_[+key+]" [+support_checked+] value="checked" />
        </td>
		<td style="text-align:center;">
            <input type="checkbox" name="tax_quick_edit[[+key+]]" id="tax_quick_edit_[+key+]" [+quick_edit_checked+] value="checked" />
        </td>
		<td style="text-align:center;">
            <input type="checkbox" name="tax_term_search[[+key+]]" id="tax_term_search_[+key+]" [+term_search_checked+] value="checked" />
        </td>
		<td style="text-align:center;">
            <input type="checkbox" name="tax_flat_checklist[[+key+]]" id="tax_flat_checklist_[+key+]" [+flat_checklist_checked+] [+flat_checklist_disabled+] value="[+flat_checklist_value+]" />
        </td>
		<td style="text-align:center;">
            <input type="checkbox" name="tax_checked_on_top[[+key+]]" id="tax_checked_on_top_[+key+]" [+checked_on_top_checked+] value="checked" />
        </td>
		<td style="text-align:center;">
            <input type="radio" name="tax_filter" id="tax_filter_[+key+]" [+filter_checked+] value="[+key+]" />
        </td>
		<td>
            &nbsp;[+name+]
        </td>
		</tr>

<!-- template="search-table" -->
        <tr valign="top">
		  <td>&nbsp;</td>
          <td class="textleft">
            <table class="searchtable">
              <tbody>
                <tr>
                  <td class="textleft">
                    <input name="search_connector" id="search-and" type="radio" [+and_checked+] value="AND" />
                    [+AND+]&nbsp;&nbsp;
                    <input name="search_connector" id="search-or" type="radio" [+or_checked+] value="OR" />
                    [+OR+]
				  </td>
                </tr>
                <tr>
                  <td class="textleft">
                    <input name="search_fields[]" id="search-title" type="checkbox" [+title_checked+] value="title" />[+Title+]&nbsp;&nbsp;&nbsp;&nbsp;
                    <input name="search_fields[]" id="search-name" type="checkbox" [+name_checked+] value="name" />[+Name+]&nbsp;&nbsp;&nbsp;&nbsp;
                    <input name="search_fields[]" id="search-alt-text" type="checkbox" [+alt_text_checked+] value="alt-text" />[+ALT Text+]&nbsp;&nbsp;&nbsp;&nbsp;
                    <input name="search_fields[]" id="search-excerpt" type="checkbox" [+excerpt_checked+] value="excerpt" />[+Caption+]&nbsp;&nbsp;&nbsp;&nbsp;
                    <input name="search_fields[]" id="search-content" type="checkbox" [+content_checked+] value="content" />[+Description+]&nbsp;&nbsp;&nbsp;&nbsp;
                    <input name="search_fields[]" id="search-terms" type="checkbox" [+terms_checked+] value="terms" />[+Terms+]
                </tr>
              </tbody>
            </table>
            <div style="font-size:8pt;padding-bottom:10px;">[+help+]</div>
          </td>
        </tr>
<!-- template="custom-field-table" -->
        <tr valign="top">
		<td colspan="2" class="textleft">
          <table class="custom-field-table">
		  <thead>
		  <tr style="text-align:center">
		    <th scope="col">
			[+Field Title+]
			</th>
		    <th scope="col">
			[+Data Source+]
			</th>
		    <th scope="col">
			[+Existing Text+]
			</th>
		    <th scope="col">
			[+Format+]
			</th>
		    <th scope="col">
			[+MLA Column+]
			</th>
		    <th scope="col">
			[+Quick Edit+]
			</th>
		    <th scope="col">
			[+Bulk Edit+]
			</th>
			</tr>
			</thead>
			<tbody>
[+table_rows+]
			</tbody>
          </table>
          <div style="font-size:8pt;padding-bottom:10px;">[+help+]</div>
        </td></tr>

<!-- template="custom-field-select-option" -->
                <option [+selected+] value="[+value+]">[+text+]</option>

<!-- template="custom-field-empty-row" -->
        <tr>
		<td colspan="[+column_count+]" style="font-weight:bold; height: 4em; text-align:center; vertical-align:middle">
		[+No Mapping Rules+]
        </td>
		</tr>

<!-- template="custom-field-rule-row" -->
        <tr valign="top">
		<td class="textleft" style="vertical-align:middle">
            [+name+]&nbsp;
			<input type="hidden" name="custom_field_mapping[[+index+]][name]" id="custom_field_name_[+index+]" value="[+name_attr+]" />
			<input type="hidden" name="custom_field_mapping[[+index+]][key]" id="custom_field_name_[+index+]" value="[+key+]" />
        </td>
		<td class="textleft">
            <select name="custom_field_mapping[[+index+]][data_source]" id="custom_field_data_source_[+index+]">
[+data_source_options+]
            </select>
        </td>
		<td class="textleft">
            <select name="custom_field_mapping[[+index+]][keep_existing]" id="custom_field_keep_existing_[+index+]">
                <option [+keep_selected+] value="1">[+Keep+]</option>
                <option [+replace_selected+] value="">[+Replace+]</option>
            </select>
        </td>
		<td class="textleft">
            <select name="custom_field_mapping[[+index+]][format]" id="custom_field_format_[+index+]">
                <option [+native_format+] value="native">[+Native+]</option>
                <option [+commas_format+] value="commas">[+Commas+]</option>
                <option [+raw_format+] value="raw">[+Raw+]</option>
            </select>
        </td>
		<td style="text-align:center;">
            <input type="checkbox" name="custom_field_mapping[[+index+]][mla_column]" id="custom_field_mla_column_[+index+]" [+mla_column_checked+] value="checked" />
        </td>
		<td style="text-align:center;">
            <input type="checkbox" name="custom_field_mapping[[+index+]][quick_edit]" id="custom_field_quick_edit_[+index+]" [+quick_edit_checked+] value="checked" />
        </td>
		<td style="text-align:center;">
            <input type="checkbox" name="custom_field_mapping[[+index+]][bulk_edit]" id="custom_field_bulk_edit_[+index+]" [+bulk_edit_checked+] value="checked" />
        </td>
		</tr>
        <tr valign="top">
		<td>&nbsp;</td>
		<td class="textleft">
            <input name="custom_field_mapping[[+index+]][meta_name]" id="custom_field_meta_name_[+index+]" type="text" size="[+meta_name_size+]" value="[+meta_name+]" />
        </td>
		<td colspan="[+column_count_meta+]" class="textleft" style="vertical-align:middle;">
			<strong>[+Option+]:</strong>&nbsp;
            <select name="custom_field_mapping[[+index+]][option]" id="custom_field_option_[+index+]">
                <option [+text_option+] value="text">[+Text+]</option>
                <option [+single_option+] value="single">[+Single+]</option>
                <option [+export_option+] value="export">[+Export+]</option>
                <option [+array_option+] value="array">[+Array+]</option>
                <option [+multi_option+] value="multi">[+Multi+]</option>
            </select>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="checkbox" name="custom_field_mapping[[+index+]][no_null]" id="custom_field_no_null_[+index+]" [+no_null_checked+] value="1" /> <strong>[+Delete NULL values+]</strong>
        </td>
		</tr>
        <tr valign="top">
		<td colspan="[+column_count+]" style="padding-bottom: 10px">
	        <input name="custom_field_mapping[[+index+]][action][delete_rule]" class="button-primary" id="custom-field-mapping-delete-rule-[+index+]" style="height: 18px; line-height: 16px" type="submit" value="[+Delete Rule+]" />
	        <input name="custom_field_mapping[[+index+]][action][delete_field]" class="button-primary" id="custom-field-mapping-delete-field-[+index+]" style="height: 18px; line-height: 16px" type="submit" value="[+Delete Field+]" />
	        <input name="custom_field_mapping[[+index+]][action][update_rule]" class="button-primary" id="custom-field-mapping-update-rule-[+index+]" style="height: 18px; line-height: 16px" type="submit" value="[+Update Rule+]" />
	        <input name="custom_field_mapping[[+index+]][action][map_now]" class="button-secondary mla-mapping" id="custom-field-mapping-map-now-[+index+]" style="height: 18px; line-height: 16px" type="submit" value="[+Map All Attachments+]" />
        </td>
		</tr>

<!-- template="custom-field-new-rule-row" -->
        <tr>
		<td colspan="[+column_count+]" style="font-weight:bold; height: 3em; vertical-align:bottom">
		[+Add new Rule+]
        </td>
		</tr>
        <tr valign="top">
		<td class="textleft">
            <select name="custom_field_mapping[[+index+]][name]" id="custom_field_name_[+index+]">
[+field_name_options+]
            </select>
        </td>
		<td class="textleft">
            <select name="custom_field_mapping[[+index+]][data_source]" id="custom_field_data_source_[+index+]">
[+data_source_options+]
            </select>
        </td>
		<td class="textleft">
            <select name="custom_field_mapping[[+index+]][keep_existing]" id="custom_field_keep_existing_[+index+]">
                <option [+keep_selected+] value="1">[+Keep+]</option>
                <option [+replace_selected+] value="">[+Replace+]</option>
            </select>
        </td>
		<td class="textleft">
            <select name="custom_field_mapping[[+index+]][format]" id="custom_field_format_[+index+]">
                <option [+native_format+] value="native">[+Native+]</option>
                <option [+commas_format+] value="commas">[+Commas+]</option>
                <option [+raw_format+] value="raw">[+Raw+]</option>
            </select>
        </td>
		<td style="text-align:center;">
            <input type="checkbox" name="custom_field_mapping[[+index+]][mla_column]" id="custom_field_mla_column_[+index+]" [+mla_column_checked+] value="checked" />
        </td>
		<td style="text-align:center;">
            <input type="checkbox" name="custom_field_mapping[[+index+]][quick_edit]" id="custom_field_quick_edit_[+index+]" [+quick_edit_checked+] value="checked" />
        </td>
		<td style="text-align:center;">
            <input type="checkbox" name="custom_field_mapping[[+index+]][bulk_edit]" id="custom_field_bulk_edit_[+index+]" [+bulk_edit_checked+] value="checked" />
        </td>
		</tr>
        <tr valign="top">
		<td>&nbsp;</td>
		<td class="textleft">
            <input name="custom_field_mapping[[+index+]][meta_name]" id="custom_field_meta_name_[+index+]" type="text" size="[+meta_name_size+]" value="[+meta_name+]" />
        </td>
		<td colspan="[+column_count_meta+]" class="textleft" style="vertical-align:middle;">
			<strong>[+Option+]:</strong>&nbsp;
            <select name="custom_field_mapping[[+index+]][option]" id="custom_field_option_[+index+]">
                <option [+text_option+] value="text">[+Text+]</option>
                <option [+single_option+] value="single">[+Single+]</option>
                <option [+export_option+] value="export">[+Export+]</option>
                <option [+array_option+] value="array">[+Array+]</option>
                <option [+multi_option+] value="multi">[+Multi+]</option>
            </select>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="checkbox" name="custom_field_mapping[[+index+]][no_null]" id="custom_field_no_null_[+index+]" [+no_null_checked+] value="1" /> <strong>[+Delete NULL values+]</strong>
        </td>
		</tr>
        <tr valign="top">
		<td colspan="[+column_count+]">
	        <input name="custom_field_mapping[[+index+]][action][add_rule]" class="button-primary" id="custom-field-mapping-add-rule-[+index+]" type="submit" value="[+Add Rule+]" />
	        <input name="custom_field_mapping[[+index+]][action][add_rule_map]" class="button-secondary mla-mapping" id="custom-field-mapping-add-rule-map-[+index+]" type="submit" value="[+Map All Attachments+]" />
        </td>
		</tr>

<!-- template="custom-field-new-field-row" -->
        <tr>
		<td colspan="[+column_count+]" style="font-weight:bold; height: 3em; vertical-align:bottom">
		[+Add new Field+]
        </td>
		</tr>
        <tr valign="top">
		<td class="textleft">
            <input name="custom_field_mapping[[+index+]][name]" id="custom_field_name_[+index+]" type="text" size="[+field_name_size+]" value="" />
        </td>
		<td class="textleft">
            <select name="custom_field_mapping[[+index+]][data_source]" id="custom_field_data_source_[+index+]">
[+data_source_options+]
            </select>
        </td>
		<td class="textleft">
            <select name="custom_field_mapping[[+index+]][keep_existing]" id="custom_field_keep_existing_[+index+]">
                <option [+keep_selected+] value="1">[+Keep+]</option>
                <option [+replace_selected+] value="">[+Replace+]</option>
            </select>
        </td>
		<td class="textleft">
            <select name="custom_field_mapping[[+index+]][format]" id="custom_field_format_[+index+]">
                <option [+native_format+] value="native">[+Native+]</option>
                <option [+commas_format+] value="commas">[+Commas+]</option>
                <option [+raw_format+] value="raw">[+Raw+]</option>
            </select>
        </td>
		<td style="text-align:center;">
            <input type="checkbox" name="custom_field_mapping[[+index+]][mla_column]" id="custom_field_mla_column_[+index+]" [+mla_column_checked+] value="checked" />
        </td>
		<td style="text-align:center;">
            <input type="checkbox" name="custom_field_mapping[[+index+]][quick_edit]" id="custom_field_quick_edit_[+index+]" [+quick_edit_checked+] value="checked" />
        </td>
		<td style="text-align:center;">
            <input type="checkbox" name="custom_field_mapping[[+index+]][bulk_edit]" id="custom_field_bulk_edit_[+index+]" [+bulk_edit_checked+] value="checked" />
        </td>
		</tr>
        <tr valign="top">
		<td>&nbsp;</td>
		<td class="textleft">
            <input name="custom_field_mapping[[+index+]][meta_name]" id="custom_field_meta_name_[+index+]" type="text" size="[+meta_name_size+]" value="[+meta_name+]" />
        </td>
		<td colspan="[+column_count_meta+]" class="textleft" style="vertical-align:middle;">
			<strong>[+Option+]:</strong>&nbsp;
            <select name="custom_field_mapping[[+index+]][option]" id="custom_field_option_[+index+]">
                <option [+text_option+] value="text">[+Text+]</option>
                <option [+single_option+] value="single">[+Single+]</option>
                <option [+export_option+] value="export">[+Export+]</option>
                <option [+array_option+] value="array">[+Array+]</option>
                <option [+multi_option+] value="multi">[+Multi+]</option>
            </select>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="checkbox" name="custom_field_mapping[[+index+]][no_null]" id="custom_field_no_null_[+index+]" [+no_null_checked+] value="1" /> <strong>[+Delete NULL values+]</strong>
        </td>
		</tr>
        <tr valign="top">
		<td colspan="[+column_count+]">
	        <input name="custom_field_mapping[[+index+]][action][add_field]" class="button-primary" id="custom-field-mapping-add-field-[+index+]" type="submit" value="[+Add Field+]" />
	        <input name="custom_field_mapping[[+index+]][action][add_field_map]" class="button-secondary mla-mapping" id="custom-field-mapping-add-field-map-[+index+]" type="submit" value="[+Map All Attachments+]" />
        </td>
		</tr>

<!-- template="iptc-exif-standard-table" -->
        <tr valign="top">
		<td colspan="2" class="textleft">
          <table class="iptc-exif-standard-table">
		  <thead>
		  <tr>
		    <th scope="col" style="text-align:center">
			[+Field Title+]
			</th>
		    <th scope="col" style="text-align:center">
			[+IPTC Value+]
			</th>
		    <th scope="col" style="text-align:center">
			[+EXIF/Template Value+]
			</th>
		    <th scope="col" class="textleft">
			[+Priority+]
			</th>
		    <th scope="col" class="textleft">
			[+Existing Text+]
			</th>
			</tr>
			</thead>
			<tbody>
[+table_rows+]
			</tbody>
          </table>
          <div style="font-size:8pt;padding-bottom:10px;">[+help+]</div>
        </td></tr>

<!-- template="iptc-exif-taxonomy-table" -->
        <tr valign="top">
		<td colspan="2" class="textleft">
          <table class="iptc-exif-taxonomy-table">
		  <thead>
		  <tr>
		    <th scope="col" style="text-align:center">
			[+Field Title+]
			</th>
		    <th scope="col" style="text-align:center">
			[+IPTC Value+]
			</th>
		    <th scope="col" style="text-align:center">
			[+EXIF/Template Value+]
			</th>
		    <th scope="col" style="text-align:center">
			[+Priority+]
			</th>
		    <th scope="col" style="text-align:center">
			[+Existing Text+]
			</th>
		    <th scope="col" style="text-align:center">
			[+Delimiter(s)+]
			</th>
		    <th scope="col" style="text-align:center">
			[+Parent+]
			</th>
			</tr>
			</thead>
			<tbody>
[+table_rows+]
			</tbody>
          </table>
          <div style="font-size:8pt;padding-bottom:10px;">[+help+]</div>
        </td></tr>

<!-- template="iptc-exif-custom-table" -->
        <tr valign="top">
		<td colspan="2" class="textleft">
          <table class="iptc-exif-custom-table">
		  <thead>
		  <tr>
		    <th scope="col" style="text-align:center">
			[+Field Title+]
			</th>
		    <th scope="col" style="text-align:center">
			[+IPTC Value+]
			</th>
		    <th scope="col" style="text-align:center">
			[+EXIF/Template Value+]
			</th>
		    <th scope="col" style="text-align:center">
			[+Priority+]
			</th>
		    <th scope="col" style="text-align:center">
			[+Existing Text+]
			</th>
			</tr>
			</thead>
			<tbody>
[+table_rows+]
			</tbody>
          </table>
          <div style="font-size:8pt;padding-bottom:10px;">[+help+]</div>
        </td></tr>

<!-- template="iptc-exif-select-option" -->
                <option [+selected+] value="[+value+]">[+text+]</option>

<!-- template="iptc-exif-select" -->
            <select name="iptc_exif_mapping[[+array+]][[+key+]][[+element+]]" id="iptc_exif_taxonomy_parent_[+key+]">
[+options+]
            </select>

<!-- template="iptc-exif-standard-row" -->
        <tr valign="top">
		<td>
            [+name+]&nbsp;
        </td>
		<td class="textleft">
            <select name="iptc_exif_mapping[standard][[+key+]][iptc_value]" id="iptc_exif_standard_iptc_field_[+key+]">
[+iptc_field_options+]
            </select>
        </td>
		<td style="text-align:center;">
            <input name="iptc_exif_mapping[standard][[+key+]][exif_value]" id="iptc_exif_standard_exif_field_[+key+]" type="text" size="[+exif_size+]" value="[+exif_text+]" />
        </td>
		<td class="textleft">
            <select name="iptc_exif_mapping[standard][[+key+]][iptc_first]" id="iptc_exif_standard_priority_[+key+]">
                <option [+iptc_selected+] value="1">[+IPTC+]</option>
                <option [+exif_selected+] value="">[+EXIF+]</option>
            </select>
        </td>
		<td class="textleft">
            <select name="iptc_exif_mapping[standard][[+key+]][keep_existing]" id="iptc_exif_standard_existing_[+key+]">
                <option [+keep_selected+] value="1">[+Keep+]</option>
                <option [+replace_selected+] value="">[+Replace+]</option>
            </select>
        </td>
		</tr>

<!-- template="iptc-exif-taxonomy-row" -->
        <tr valign="top">
		<td>
            [+name+]&nbsp;
			<input type="hidden" id="iptc_exif_taxonomy_name_field_[+key+]" name="iptc_exif_mapping[taxonomy][[+key+]][name]" value="[+name+]" />
			<input type="hidden" id="iptc_exif_taxonomy_hierarchical_field_[+key+]" name="iptc_exif_mapping[taxonomy][[+key+]][hierarchical]" value="[+hierarchical+]" />
        </td>
		<td class="textleft">
            <select name="iptc_exif_mapping[taxonomy][[+key+]][iptc_value]" id="iptc_exif_taxonomy_iptc_field_[+key+]">
[+iptc_field_options+]
            </select>
        </td>
		<td style="text-align:center;">
            <input name="iptc_exif_mapping[taxonomy][[+key+]][exif_value]" id="iptc_exif_taxonomy_exif_field_[+key+]" type="text" size="[+exif_size+]" value="[+exif_text+]" />
        </td>
		<td class="textleft">
            <select name="iptc_exif_mapping[taxonomy][[+key+]][iptc_first]" id="iptc_exif_taxonomy_priority_[+key+]">
                <option [+iptc_selected+] value="1">[+IPTC+]</option>
                <option [+exif_selected+] value="">[+EXIF+]</option>
            </select>
        </td>
		<td class="textleft">
            <select name="iptc_exif_mapping[taxonomy][[+key+]][keep_existing]" id="iptc_exif_taxonomy_existing_[+key+]">
                <option [+keep_selected+] value="1">[+Keep+]</option>
                <option [+replace_selected+] value="">[+Replace+]</option>
            </select>
        </td>
		<td style="text-align:center;">
            <input name="iptc_exif_mapping[taxonomy][[+key+]][delimiters]" id="iptc_exif_taxonomy_delimiters_[+key+]" type="text" size="[+delimiters_size+]" value="[+delimiters_text+]" />
        </td>
		<td class="textleft">
[+parent_select+]
        </td>
		</tr>

<!-- template="iptc-exif-custom-empty-row" -->
        <tr>
		<td colspan="[+column_count+]" style="font-weight:bold; height: 4em; text-align:center; vertical-align:middle">
		[+No Mapping Rules+]
        </td>
		</tr>

<!-- template="iptc-exif-custom-rule-row" -->
        <tr valign="top">
		<td class="textleft" style="vertical-align:middle">
            [+name+]&nbsp;
			<input name="iptc_exif_mapping[custom][[+index+]][name]" id="iptc_exif_custom_name_[+index+]" type="hidden" value="[+name_attr+]" />
			<input name="iptc_exif_mapping[custom][[+index+]][key]" id="iptc_exif_custom_key_[+index+]" type="hidden" value="[+key+]" />
        </td>
		<td class="textleft">
            <select name="iptc_exif_mapping[custom][[+index+]][iptc_value]" id="iptc_exif_custom_iptc_field_[+index+]">
[+iptc_field_options+]
            </select>
        </td>
		<td style="text-align:center;">
            <input name="iptc_exif_mapping[custom][[+index+]][exif_value]" id="iptc_exif_custom_exif_field_[+index+]" type="text" size="[+exif_size+]" value="[+exif_text+]" />
        </td>
		<td class="textleft">
            <select name="iptc_exif_mapping[custom][[+index+]][iptc_first]" id="iptc_exif_custom_priority_[+index+]">
                <option [+iptc_selected+] value="1">[+IPTC+]</option>
                <option [+exif_selected+] value="">[+EXIF+]</option>
            </select>
        </td>
		<td class="textleft">
            <select name="iptc_exif_mapping[custom][[+index+]][keep_existing]" id="iptc_exif_custom_existing_[+index+]">
                <option [+keep_selected+] value="1">[+Keep+]</option>
                <option [+replace_selected+] value="">[+Replace+]</option>
            </select>
        </td>
		</tr>
		<tr valign="top">
		<td>&nbsp;</td>
		<td class="textright">
			<strong>[+Format+]:</strong>&nbsp;
            <select name="iptc_exif_mapping[custom][[+index+]][format]" id="iptc_exif_custom_format_[+index+]">
                <option [+native_format+] value="native">[+Native+]</option>
                <option [+commas_format+] value="commas">[+Commas+]</option>
                <option [+raw_format+] value="raw">[+Raw+]</option>
            </select>
        </td>
		<td colspan="[+column_count_meta+]" class="textleft" style="vertical-align:middle;">
			<strong>[+Option+]:</strong>&nbsp;
            <select name="iptc_exif_mapping[custom][[+index+]][option]" id="iptc_exif_custom_option_[+index+]">
                <option [+text_option+] value="text">[+Text+]</option>
                <option [+single_option+] value="single">[+Single+]</option>
                <option [+export_option+] value="export">[+Export+]</option>
                <option [+array_option+] value="array">[+Array+]</option>
                <option [+multi_option+] value="multi">[+Multi+]</option>
            </select>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="checkbox" name="iptc_exif_mapping[custom][[+index+]][no_null]" id="iptc_exif_custom_no_null_[+index+]" [+no_null_checked+] value="1" /> <strong>[+Delete NULL values+]</strong>
        </td>
		</tr>
		<tr valign="top">
		<td colspan="[+column_count+]" style="padding-bottom: 10px">
	        <input name="iptc_exif_mapping[custom][[+index+]][action][delete_rule]" class="button-primary" id="iptc-exif-mapping-delete-rule-[+index+]" style="height: 18px; line-height: 16px" type="submit" value="[+Delete Rule+]" />
	        <input name="iptc_exif_mapping[custom][[+index+]][action][delete_field]" class="button-primary" id="iptc-exif-mapping-delete-field-[+index+]" style="height: 18px; line-height: 16px" type="submit" value="[+Delete Field+]" />
	        <input name="iptc_exif_mapping[custom][[+index+]][action][update_rule]" class="button-primary" id="iptc-exif-mapping-update-rule-[+index+]" style="height: 18px; line-height: 16px" type="submit" value="[+Update Rule+]" />
	        <input name="iptc_exif_mapping[custom][[+index+]][action][map_now]" class="button-secondary mla-mapping" id="iptc-exif-mapping-map-now-[+index+]" style="height: 18px; line-height: 16px" type="submit" value="[+Map All Attachments+]" />
        </td>
		</tr>

<!-- template="iptc-exif-custom-new-rule-row" -->
        <tr>
		<td colspan="[+column_count+]" style="font-weight:bold; height: 3em; vertical-align:bottom">
		[+Add new Rule+]
        </td>
		</tr>
        <tr valign="top">
		<td class="textleft">
            <select name="iptc_exif_mapping[custom][[+index+]][name]" id="iptc_exif_custom_name_[+index+]">
[+field_name_options+]
            </select>
        </td>
		<td class="textleft">
            <select name="iptc_exif_mapping[custom][[+index+]][iptc_value]" id="iptc_exif_custom_iptc_field_[+index+]">
[+iptc_field_options+]
            </select>
        </td>
		<td style="text-align:center;">
            <input name="iptc_exif_mapping[custom][[+index+]][exif_value]" id="iptc_exif_custom_exif_field_[+index+]" type="text" size="[+exif_size+]" value="[+exif_text+]" />
        </td>
		<td class="textleft">
            <select name="iptc_exif_mapping[custom][[+index+]][iptc_first]" id="iptc_exif_custom_priority_[+index+]">
                <option [+iptc_selected+] value="1">[+IPTC+]</option>
                <option [+exif_selected+] value="">[+EXIF+]</option>
            </select>
        </td>
		<td class="textleft">
            <select name="iptc_exif_mapping[custom][[+index+]][keep_existing]" id="iptc_exif_custom_existing_[+index+]">
                <option [+keep_selected+] value="1">[+Keep+]</option>
                <option [+replace_selected+] value="">[+Replace+]</option>
            </select>
        </td>
		</tr>
		<tr valign="top">
		<td>&nbsp;</td>
		<td class="textright">
			<strong>[+Format+]:</strong>&nbsp;
            <select name="iptc_exif_mapping[custom][[+index+]][format]" id="iptc_exif_custom_format_[+index+]">
                <option [+native_format+] value="native">[+Native+]</option>
                <option [+commas_format+] value="commas">[+Commas+]</option>
                <option [+raw_format+] value="raw">[+Raw+]</option>
            </select>
        </td>
		<td colspan="[+column_count_meta+]" class="textleft" style="vertical-align:middle;">
			<strong>[+Option+]:</strong>&nbsp;
            <select name="iptc_exif_mapping[custom][[+index+]][option]" id="iptc_exif_custom_option_[+index+]">
                <option [+text_option+] value="text">[+Text+]</option>
                <option [+single_option+] value="single">[+Single+]</option>
                <option [+export_option+] value="export">[+Export+]</option>
                <option [+array_option+] value="array">[+Array+]</option>
                <option [+multi_option+] value="multi">[+Multi+]</option>
            </select>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="checkbox" name="iptc_exif_mapping[custom][[+index+]][no_null]" id="iptc_exif_custom_no_null_[+index+]" [+no_null_checked+] value="1" /> <strong>[+Delete NULL values+]</strong>
        </td>
		</tr>
        <tr valign="top">
		<td colspan="[+column_count+]">
	        <input name="iptc_exif_mapping[custom][[+index+]][action][add_rule]" class="button-primary" id="iptc-exif-mapping-add-rule-[+index+]" type="submit" value="[+Add Rule+]" />
	        <input name="iptc_exif_mapping[custom][[+index+]][action][add_rule_map]" class="button-secondary mla-mapping" id="iptc-exif-mapping-add-rule-map-[+index+]" type="submit" value="[+Map All Attachments+]" />
        </td>
		</tr>

<!-- template="iptc-exif-custom-new-field-row" -->
        <tr>
		<td colspan="[+column_count+]" style="font-weight:bold; height: 3em; vertical-align:bottom">
		[+Add new Field+]
        </td>
		</tr>
        <tr valign="top">
		<td class="textleft">
            <input name="iptc_exif_mapping[custom][[+index+]][name]" id="iptc_exif_custom_name_[+index+]" type="text" size="[+field_name_size+]" value="" />
        </td>
		<td class="textleft">
            <select name="iptc_exif_mapping[custom][[+index+]][iptc_value]" id="iptc_exif_custom_iptc_field_[+index+]">
[+iptc_field_options+]
            </select>
        </td>
		<td style="text-align:center;">
            <input name="iptc_exif_mapping[custom][[+index+]][exif_value]" id="iptc_exif_custom_exif_field_[+index+]" type="text" size="[+exif_size+]" value="[+exif_text+]" />
        </td>
		<td class="textleft">
            <select name="iptc_exif_mapping[custom][[+index+]][iptc_first]" id="iptc_exif_custom_priority_[+index+]">
                <option [+iptc_selected+] value="1">[+IPTC+]</option>
                <option [+exif_selected+] value="">[+EXIF+]</option>
            </select>
        </td>
		<td class="textleft">
            <select name="iptc_exif_mapping[custom][[+index+]][keep_existing]" id="iptc_exif_custom_existing_[+index+]">
                <option [+keep_selected+] value="1">[+Keep+]</option>
                <option [+replace_selected+] value="">[+Replace+]</option>
            </select>
        </td>
		</tr>
		<tr valign="top">
		<td>&nbsp;</td>
		<td class="textright">
			<strong>[+Format+]:</strong>&nbsp;
            <select name="iptc_exif_mapping[custom][[+index+]][format]" id="iptc_exif_custom_format_[+index+]">
                <option [+native_format+] value="native">[+Native+]</option>
                <option [+commas_format+] value="commas">[+Commas+]</option>
                <option [+raw_format+] value="raw">[+Raw+]</option>
            </select>
        </td>
		<td colspan="[+column_count_meta+]" class="textleft" style="vertical-align:middle;">
			<strong>[+Option+]:</strong>&nbsp;
            <select name="iptc_exif_mapping[custom][[+index+]][option]" id="iptc_exif_custom_option_[+index+]">
                <option [+text_option+] value="text">[+Text+]</option>
                <option [+single_option+] value="single">[+Single+]</option>
                <option [+export_option+] value="export">[+Export+]</option>
                <option [+array_option+] value="array">[+Array+]</option>
                <option [+multi_option+] value="multi">[+Multi+]</option>
            </select>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="checkbox" name="iptc_exif_mapping[custom][[+index+]][no_null]" id="iptc_exif_custom_no_null_[+index+]" [+no_null_checked+] value="1" /> <strong>[+Delete NULL values+]</strong>
        </td>
		</tr>
        <tr valign="top">
		<td colspan="[+column_count+]">
	        <input name="iptc_exif_mapping[custom][[+index+]][action][add_field]" class="button-primary" id="iptc-exif-mapping-add-field-[+index+]" type="submit" value="[+Add Field+]" />
	        <input name="iptc_exif_mapping[custom][[+index+]][action][add_field_map]" class="button-secondary mla-mapping" id="iptc-exif-mapping-add-field-map-[+index+]" type="submit" value="[+Map All Attachments+]" />
        </td>
		</tr>
