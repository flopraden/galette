{extends file="./select.tpl"}

{block name="element"}
    <select name="activite_adh" id="activite_adh"{if isset($disabled.activite_adh)} {$disabled.activite_adh}{/if}{if isset($required.activite_adh) and $required.activite_adh eq 1} required{/if}>
        <option value="1" {if $member->isActive() eq 1}selected="selected"{/if}>{_T string="Active"}</option>
        <option value="0" {if $member->isActive() eq 0}selected="selected"{/if}>{_T string="Inactive"}</option>
    </select>
{/block}
