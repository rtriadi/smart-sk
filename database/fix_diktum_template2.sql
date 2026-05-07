-- Update the Menetapkan section to show proper header + diktum items
-- The current format has Menetapkan : {{diktum_placeholder}}
-- We need: Menetapkan : KEPUTUSAN {jabatan} TENTANG {judul} followed by KESATU, KEDUA etc.

UPDATE tb_templates 
SET html_pattern = REPLACE(
    html_pattern,
    CONCAT(
        '<table style="width: 100%; border: none; margin-bottom: 20px;">\r\n',
        '<tbody>\r\n',
        '<tr>\r\n',
        '<td style="width: 120px; vertical-align: top; border: none; padding: 5px 0;">Menetapkan</td>\r\n',
        '<td style="width: 20px; vertical-align: top; border: none; padding: 5px 0;">:</td>\r\n',
        '<td style="vertical-align: top; border: none; padding: 5px 0;">{{diktum_placeholder}}</td>\r\n',
        '</tr>\r\n',
        '</tbody>\r\n',
        '</table>'
    ),
    CONCAT(
        '<table style="width: 100%; border: none; margin-bottom: 5px;">\r\n',
        '<tbody>\r\n',
        '<tr>\r\n',
        '<td style="width: 120px; vertical-align: top; border: none; padding: 5px 0;">Menetapkan</td>\r\n',
        '<td style="width: 20px; vertical-align: top; border: none; padding: 5px 0;">:</td>\r\n',
        '<td style="vertical-align: top; border: none; padding: 5px 0;"><strong>KEPUTUSAN {{jabatan_penandatangan}} TENTANG {{judul_sk}}</strong></td>\r\n',
        '</tr>\r\n',
        '</tbody>\r\n',
        '</table>\r\n',
        '{{diktum_section}}'
    )
)
WHERE id = 1;
