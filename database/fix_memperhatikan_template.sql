UPDATE tb_templates 
SET html_pattern = REPLACE(
    html_pattern,
    '<ol type="1" style="margin: 0; padding-left: 20px;">\r\n{{#each list_memperhatikan}}<li>{{this}}</li>{{/each}}\r\n</ol>',
    '{{memperhatikan}}'
)
WHERE id = 1;
