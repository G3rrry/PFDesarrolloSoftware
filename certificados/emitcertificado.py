#!/usr/bin/env python3

import sys

import jinja2
import pdfkit
import json
import base64



path = sys.argv[1]
context = sys.argv[2]
format_html = sys.argv[3]
image_path = sys.argv[4] if len(sys.argv) > 4 else None

context = json.loads(context)

template_loader = jinja2.FileSystemLoader('/var/www/html/ds/certificados/')
template_env = jinja2.Environment(loader=template_loader)
template = template_env.get_template(format_html)

if image_path:
    with open(image_path, 'rb') as f:
        image_path = base64.b64encode(f.read()).decode()

output_text = template.render(context, img_string=image_path)

config = pdfkit.configuration(wkhtmltopdf='/usr/local/bin/wkhtmltopdf')
pdfkit.from_string(output_text, path, configuration=config)