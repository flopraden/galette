{include
    file="./checkbox.tpl"
    name="is_company"
    id="is_company"
    value="1"
    label={_T string="Is company?"}
    title={_T string="Is member a company?"}
    tip={_T string="Do you manage a non profit organization, or a company? If you do so, check the box, and then enter its name in the field that will appear."}
}

{include
    file="forms_types/text.tpl"
    name=$entry->field_id
    id=$entry->field_id
    value=$member->company_name
    required=$entry->required
    label=$entry->label
    component_id="company_field"
    component_class="hidden"
}
