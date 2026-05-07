-- Fix hijriah date: show on separate line below Masehi, with M suffix
-- Remove the old inline " / {{tanggal_hijri}}" format
UPDATE tb_templates 
SET html_pattern = REPLACE(
    html_pattern, 
    '<p style="margin: 0;">Pada tanggal {{tanggal_indo}} / {{tanggal_hijri}}</p>', 
    '<p style="margin: 0;">Pada tanggal {{tanggal_indo}}</p>\n<p style="margin: 0;">{{tanggal_hijri}}</p>'
) 
WHERE id = 1;
