[{$smarty.block.parent}]
<tr>
    <td class="edittext" >
        [{oxmultilang ident="PB_OWNER_EMAIL_RECEIVER"}]
    </td>
    <td class="edittext">
        <input type="text" class="editinput" size="35" name="editval[oxshops__pbowneremailreceiver]" value="[{$edit->oxshops__pbowneremailreceiver->value}]" [{$readonly}]>
        [{oxinputhelp ident="HELP_PB_OWNER_EMAIL_RECEIVER"}]
    </td>
</tr>
