<p>
    <span class="bline">{_T string="Picture:"}</span>
    <img id="photo_adh" src="{$galette_base_path}picture.php?id_adh={$member->id}&amp;rand={$time}" class="picture" width="{$member->picture->getOptimalWidth()}" height="{$member->picture->getOptimalHeight()}" alt="{_T string="Picture"}"/><br/>
{if $member->hasPicture() eq 1 }
    <label for="del_photo" class="labelalign">{_T string="Delete image"}</label> <input type="checkbox" name="del_photo" id="del_photo" value="1"/><br/>
{/if}
    <input class="labelalign" type="file" name="photo"/>
</p>
