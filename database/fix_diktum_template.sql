-- Step 1: Update form_config to change isi_memutuskan (textarea) to list_diktum (repeater)
UPDATE tb_templates 
SET form_config = REPLACE(
    form_config,
    '{"variable":"isi_memutuskan","label":"Isi Memutuskan/Menetapkan","type":"textarea"}',
    '{"variable":"list_diktum","label":"Diktum (KESATU, KEDUA, dst)","type":"repeater"}'
)
WHERE id = 1;

-- Step 2: Update html_pattern to use diktum repeater format
UPDATE tb_templates 
SET html_pattern = REPLACE(
    html_pattern,
    '<td style="vertical-align: top; border: none; padding: 5px 0;">{{isi_memutuskan}}</td>',
    '<td style="vertical-align: top; border: none; padding: 5px 0;">{{diktum_placeholder}}</td>'
)
WHERE id = 1;
