# -*- coding: utf-8 -*-
"""
Script to generate the complete academic report DOCX for GiaDungPro Ecommerce Website
following the templates:
1. Mẫu hướng dẫn trình bày báo cáo (4).docx
2. HOVATEN_MSSV.docx
"""

import os
import sys
import docx
from docx import Document
from docx.shared import Inches, Pt, RGBColor, Cm
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT, WD_ALIGN_VERTICAL
from docx.oxml import parse_xml, OxmlElement
from docx.oxml.ns import nsdecls, qn

def create_report():
    doc = Document()

    # --- Page setup (A4, Margins: Top=2cm, Bottom=2cm, Left=3cm, Right=2cm) ---
    section = doc.sections[0]
    section.page_width = Cm(21.0)
    section.page_height = Cm(29.7)
    section.top_margin = Cm(2.0)
    section.bottom_margin = Cm(2.0)
    section.left_margin = Cm(3.0)
    section.right_margin = Cm(2.0)

    # Base styling
    normal_style = doc.styles['Normal']
    normal_font = normal_style.font
    normal_font.name = 'Times New Roman'
    normal_font.size = Pt(13)
    normal_font.color.rgb = RGBColor(0, 0, 0)
    normal_style.paragraph_format.line_spacing = 1.3
    normal_style.paragraph_format.space_after = Pt(4)
    normal_style.paragraph_format.space_before = Pt(0)

    # Colors
    COLOR_PRIMARY = RGBColor(14, 116, 144)    # Cyan / Dark Blue
    COLOR_SECONDARY = RGBColor(30, 41, 59)   # Slate 800
    COLOR_HEADER_BG = "E6F0FA"              # Soft Blue-Gray
    COLOR_HEADER_TEXT = RGBColor(15, 23, 42) # Slate 900
    COLOR_BORDER = "94A3B8"                 # Slate 400

    def add_para(text="", style=None, font_size=13, bold=False, italic=False, 
                 align=WD_ALIGN_PARAGRAPH.LEFT, space_before=0, space_after=4, 
                 line_spacing=1.3, color=RGBColor(0, 0, 0)):
        p = doc.add_paragraph(text, style=style)
        p.alignment = align
        p.paragraph_format.space_before = Pt(space_before)
        p.paragraph_format.space_after = Pt(space_after)
        p.paragraph_format.line_spacing = line_spacing
        if text:
            for run in p.runs:
                run.font.name = 'Times New Roman'
                run.font.size = Pt(font_size)
                run.bold = bold
                run.italic = italic
                run.font.color.rgb = color
        return p

    def add_heading_1(text):
        p = add_para(text, bold=True, font_size=16, color=RGBColor(15, 23, 42),
                     align=WD_ALIGN_PARAGRAPH.LEFT, space_before=14, space_after=6)
        return p

    def add_heading_2(text):
        p = add_para(text, bold=True, font_size=14, color=RGBColor(30, 58, 138),
                     align=WD_ALIGN_PARAGRAPH.LEFT, space_before=10, space_after=4)
        return p

    def add_heading_3(text):
        p = add_para(text, bold=True, italic=False, font_size=13, color=RGBColor(51, 65, 85),
                     align=WD_ALIGN_PARAGRAPH.LEFT, space_before=6, space_after=3)
        return p

    def add_heading_4(text):
        p = add_para(text, bold=True, italic=True, font_size=13, color=RGBColor(71, 85, 105),
                     align=WD_ALIGN_PARAGRAPH.LEFT, space_before=4, space_after=2)
        return p

    def set_cell_background(cell, fill_hex):
        tcPr = cell._tc.get_or_add_tcPr()
        tcPr.append(parse_xml(f'<w:shd {nsdecls("w")} w:fill="{fill_hex}"/>'))

    def set_table_borders(table):
        tblPr = table._tbl.tblPr
        borders_xml = f'''
        <w:tblBorders {nsdecls("w")}>
            <w:top w:val="single" w:sz="6" w:space="0" w:color="{COLOR_BORDER}"/>
            <w:bottom w:val="single" w:sz="6" w:space="0" w:color="{COLOR_BORDER}"/>
            <w:insideH w:val="single" w:sz="4" w:space="0" w:color="CBD5E1"/>
            <w:insideV w:val="single" w:sz="4" w:space="0" w:color="CBD5E1"/>
            <w:left w:val="single" w:sz="6" w:space="0" w:color="{COLOR_BORDER}"/>
            <w:right w:val="single" w:sz="6" w:space="0" w:color="{COLOR_BORDER}"/>
        </w:tblBorders>
        '''
        tblPr.append(parse_xml(borders_xml))

    def format_table(table, header_bg=COLOR_HEADER_BG, col_widths=None):
        table.alignment = WD_TABLE_ALIGNMENT.CENTER
        set_table_borders(table)
        for r_idx, row in enumerate(table.rows):
            # Keep row together
            trPr = row._tr.get_or_add_trPr()
            trPr.append(parse_xml(f'<w:cantSplit {nsdecls("w")}/>'))
            if r_idx == 0:
                trPr.append(parse_xml(f'<w:tblHeader {nsdecls("w")}/>'))

            for c_idx, cell in enumerate(row.cells):
                cell.vertical_alignment = WD_ALIGN_VERTICAL.CENTER
                tcPr = cell._tc.get_or_add_tcPr()
                # cell padding
                tcMar = parse_xml(f'''
                <w:tcMar {nsdecls("w")}>
                    <w:top w:w="120" w:type="dxa"/>
                    <w:bottom w:w="120" w:type="dxa"/>
                    <w:left w:w="160" w:type="dxa"/>
                    <w:right w:w="160" w:type="dxa"/>
                </w:tcMar>
                ''')
                tcPr.append(tcMar)
                if r_idx == 0:
                    set_cell_background(cell, header_bg)
                    for p in cell.paragraphs:
                        p.alignment = WD_ALIGN_PARAGRAPH.CENTER
                        for run in p.runs:
                            run.font.name = 'Times New Roman'
                            run.font.size = Pt(12)
                            run.bold = True
                else:
                    if r_idx % 2 == 1:
                        set_cell_background(cell, "F8FAFC")
                    for p in cell.paragraphs:
                        for run in p.runs:
                            run.font.name = 'Times New Roman'
                            run.font.size = Pt(12)

        if col_widths:
            for row in table.rows:
                for c_idx, width in enumerate(col_widths):
                    if c_idx < len(row.cells):
                        row.cells[c_idx].width = width

    # =========================================================================
    # 1. TRANG BÌA CHÍNH (COVER PAGE)
    # =========================================================================
    p = add_para("TRƯỜNG CAO ĐẲNG CÔNG NGHỆ THÔNG TIN TP.HCM", bold=True, font_size=16, 
                 align=WD_ALIGN_PARAGRAPH.CENTER, space_before=10, space_after=4)
    p = add_para("KHOA CÔNG NGHỆ THÔNG TIN", bold=True, font_size=14, 
                 align=WD_ALIGN_PARAGRAPH.CENTER, space_before=0, space_after=20)

    # Line separator
    add_para("------------------------***", align=WD_ALIGN_PARAGRAPH.CENTER, space_after=40)

    add_para("BÁO CÁO ĐỒ ÁN MÔN HỌC", bold=True, font_size=16, 
             align=WD_ALIGN_PARAGRAPH.CENTER, space_before=20, space_after=10, color=RGBColor(30, 41, 59))
    add_para("CHUYÊN NGÀNH: CÔNG NGHỆ THÔNG TIN", bold=True, font_size=14, 
             align=WD_ALIGN_PARAGRAPH.CENTER, space_before=0, space_after=25)

    add_para("ĐỀ TÀI:", bold=True, font_size=16, align=WD_ALIGN_PARAGRAPH.CENTER, space_before=10, space_after=8)
    add_para("XÂY DỰNG WEBSITE THƯƠNG MẠI ĐIỆN TỬ\nBÁN ĐỒ GIA DỤNG CAO CẤP - GIADUNGPRO", 
             bold=True, font_size=20, align=WD_ALIGN_PARAGRAPH.CENTER, 
             space_before=0, space_after=40, color=RGBColor(15, 76, 129), line_spacing=1.3)

    add_para("-------------------------------------------------------------------------", 
             align=WD_ALIGN_PARAGRAPH.CENTER, space_after=40)

    table_info = doc.add_table(rows=4, cols=2)
    table_info.alignment = WD_TABLE_ALIGNMENT.CENTER
    table_info.rows[0].cells[0].paragraphs[0].text = "Giảng viên hướng dẫn:"
    table_info.rows[0].cells[1].paragraphs[0].text = "KS. Huỳnh Luân"
    table_info.rows[1].cells[0].paragraphs[0].text = "Sinh viên thực hiện:"
    table_info.rows[1].cells[1].paragraphs[0].text = "Trần Thị Tèo (MSSV: 20260101)\nTrần Thị Tý (MSSV: 20260102)"
    table_info.rows[2].cells[0].paragraphs[0].text = "Lớp:"
    table_info.rows[2].cells[1].paragraphs[0].text = "CD22-CNTT01"
    table_info.rows[3].cells[0].paragraphs[0].text = "Khoá học:"
    table_info.rows[3].cells[1].paragraphs[0].text = "2024 - 2027"

    for r_idx, row in enumerate(table_info.rows):
        for c_idx, cell in enumerate(row.cells):
            p = cell.paragraphs[0]
            p.paragraph_format.line_spacing = 1.3
            p.paragraph_format.space_after = Pt(3)
            for run in p.runs:
                run.font.name = 'Times New Roman'
                run.font.size = Pt(13)
                if c_idx == 0:
                    run.bold = True
                elif r_idx == 0 and c_idx == 1:
                    run.bold = True

    add_para("", space_after=60)
    add_para("TP. Hồ Chí Minh, Năm 2026", bold=True, font_size=13, align=WD_ALIGN_PARAGRAPH.CENTER)

    doc.add_page_break()

    # =========================================================================
    # 2. TRANG BÌA PHỤ (BÌA LÓT)
    # =========================================================================
    add_para("TRƯỜNG CAO ĐẲNG CÔNG NGHỆ THÔNG TIN TP.HCM", bold=True, font_size=16, 
             align=WD_ALIGN_PARAGRAPH.CENTER, space_before=10, space_after=4)
    add_para("KHOA CÔNG NGHỆ THÔNG TIN", bold=True, font_size=14, 
             align=WD_ALIGN_PARAGRAPH.CENTER, space_before=0, space_after=20)
    add_para("------------------------***", align=WD_ALIGN_PARAGRAPH.CENTER, space_after=40)

    add_para("BÁO CÁO ĐỒ ÁN MÔN HỌC", bold=True, font_size=16, 
             align=WD_ALIGN_PARAGRAPH.CENTER, space_before=20, space_after=10)
    add_para("CHUYÊN NGÀNH: CÔNG NGHỆ THÔNG TIN", bold=True, font_size=14, 
             align=WD_ALIGN_PARAGRAPH.CENTER, space_before=0, space_after=25)

    add_para("ĐỀ TÀI:", bold=True, font_size=16, align=WD_ALIGN_PARAGRAPH.CENTER, space_before=10, space_after=8)
    add_para("XÂY DỰNG WEBSITE THƯƠNG MẠI ĐIỆN TỬ\nBÁN ĐỒ GIA DỤNG CAO CẤP - GIADUNGPRO", 
             bold=True, font_size=20, align=WD_ALIGN_PARAGRAPH.CENTER, 
             space_before=0, space_after=40, color=RGBColor(15, 76, 129))

    table_info2 = doc.add_table(rows=4, cols=2)
    table_info2.alignment = WD_TABLE_ALIGNMENT.CENTER
    table_info2.rows[0].cells[0].paragraphs[0].text = "Giảng viên hướng dẫn:"
    table_info2.rows[0].cells[1].paragraphs[0].text = "KS. Huỳnh Luân"
    table_info2.rows[1].cells[0].paragraphs[0].text = "Sinh viên thực hiện:"
    table_info2.rows[1].cells[1].paragraphs[0].text = "Trần Thị Tèo (MSSV: 20260101)\nTrần Thị Tý (MSSV: 20260102)"
    table_info2.rows[2].cells[0].paragraphs[0].text = "Lớp:"
    table_info2.rows[2].cells[1].paragraphs[0].text = "CD22-CNTT01"
    table_info2.rows[3].cells[0].paragraphs[0].text = "Khoá học:"
    table_info2.rows[3].cells[1].paragraphs[0].text = "2024 - 2027"

    for r_idx, row in enumerate(table_info2.rows):
        for c_idx, cell in enumerate(row.cells):
            p = cell.paragraphs[0]
            p.paragraph_format.line_spacing = 1.3
            p.paragraph_format.space_after = Pt(3)
            for run in p.runs:
                run.font.name = 'Times New Roman'
                run.font.size = Pt(13)
                if c_idx == 0:
                    run.bold = True
                elif r_idx == 0 and c_idx == 1:
                    run.bold = True

    add_para("", space_after=60)
    add_para("TP. Hồ Chí Minh, Năm 2026", bold=True, font_size=13, align=WD_ALIGN_PARAGRAPH.CENTER)

    doc.add_page_break()

    # =========================================================================
    # 3. LỜI MỞ ĐẦU
    # =========================================================================
    add_para("MỞ ĐẦU", bold=True, font_size=16, align=WD_ALIGN_PARAGRAPH.CENTER, space_before=10, space_after=14)

    add_para("Trong kỷ nguyên chuyển đổi số và bùng nổ của thương mại điện tử (E-Commerce) hiện nay, xu hướng mua sắm trực tuyến đã trở thành một phần thiết yếu trong đời sống hàng ngày của người tiêu dùng. Đặc biệt, ngành hàng thiết bị đồ gia dụng gia đình thông minh (nồi chiên không dầu, máy xay sinh tố, nồi cơm điện cao tần, robot hút bụi, thiết bị lọc nước và làm mát không khí) đang chứng kiến tốc độ tăng trưởng vượt bậc do nhu cầu nâng cao chất lượng cuộc sống và tiện nghi gia đình ngày càng tăng.")

    add_para("Tuy nhiên, việc kinh doanh và mua sắm các sản phẩm đồ gia dụng trên các sàn thương mại điện tử đại trà hiện nay đang đối mặt với nhiều rào cản và thách thức lớn:")
    add_para("• Thiếu chiều sâu trải nghiệm thị giác: Khách hàng khó hình dung sản phẩm thực tế khi đặt vào không gian bếp hay phòng khách gia đình.")
    add_para("• Tính phức tạp của cấu hình sản phẩm: Mỗi thiết bị gia dụng thường có nhiều biến thể phức tạp (màu sắc, dung tích, công suất, chất liệu) cùng chính sách bảo hành chính hãng đi kèm.")
    add_para("• Rủi ro tồn kho và quy trình xử lý đơn hàng thủ công: Các doanh nghiệp gia dụng thường gặp khó khăn trong việc đồng bộ hóa tồn kho thời gian thực, quản lý ca trực của nhân viên bán hàng và điều phối đơn hàng.")
    add_para("• Thiếu tính tương tác và gắn kết: Khách hàng thường chỉ ghé thăm một lần và ít quay lại nếu website thiếu các cơ chế chăm sóc khách hàng thân thiết (Loyalty Program) hay các hoạt động tương tác giải trí (Gamification).")

    add_para("Xuất phát từ thực tiễn nêu trên, nhóm chúng em đã lựa chọn và bắt tay thực hiện đề tài: “Xây dựng website thương mại điện tử bán đồ gia dụng cao cấp - GiaDụngPro”. Dự án được định hướng xây dựng trên nền tảng công nghệ hiện đại nhất hiện nay gồm Laravel 12 (PHP 8.3+), Tailwind CSS v4, Vite 7 và hệ quản trị cơ sở dữ liệu quan hệ MySQL 8.4.")

    add_para("Đồ án không chỉ tập trung giải quyết trọn vẹn bài toán nghiệp vụ mua sắm, thanh toán địa giới hành chính Việt Nam 3 cấp, phân quyền đa cấp bậc (Admin, Quản lý kho, Nhân viên bán hàng, Khách hàng), mà còn tiên phong tích hợp các trải nghiệm người dùng đột phá như: Giao diện trượt chéo rạp hát (Theatrical Diagonal Sliding Auth), Banner Parallax 3D tương tác không gian, 3D Coverflow Carousel thương hiệu đối tác, và Hệ sinh thái trò chơi tích điểm đổi quà (Gamification) phong phú.")

    add_para("Báo cáo đồ án được cấu trúc khoa học thành 4 chương chính:")
    add_para("• Chương 1: Tổng quan đề tài và Cơ sở lý thuyết.")
    add_para("• Chương 2: Phân tích và Thiết kế hệ thống.")
    add_para("• Chương 3: Cài đặt thực nghiệm và Kết quả đạt được.")
    add_para("• Chương 4: Kết luận và Hướng phát triển.")

    doc.add_page_break()

    # =========================================================================
    # 4. LỜI CẢM ƠN
    # =========================================================================
    add_para("LỜI CẢM ƠN", bold=True, font_size=16, align=WD_ALIGN_PARAGRAPH.CENTER, space_before=10, space_after=14)

    add_para("Để hoàn thành đồ án môn học này một cách chỉn chu và đạt kết quả tốt nhất, chúng em xin bày tỏ lòng biết ơn sâu sắc và chân thành nhất đến Ban Giám hiệu cùng toàn thể quý Thầy, Cô Khoa Công nghệ Thông tin - Trường Cao đẳng Công nghệ Thông tin TP.HCM. Quý Thầy Cô đã luôn tận tâm truyền đạt những kiến thức chuyên môn quý báu, chia sẻ những kinh nghiệm thực tế sâu sắc và rèn luyện cho chúng em tác phong làm việc nghiêm túc, khoa học trong suốt thời gian học tập tại trường.")

    add_para("Đặc biệt, chúng em xin gửi lời cảm ơn trân trọng và sâu sắc nhất đến Giảng viên hướng dẫn: KS. Huỳnh Luân. Thầy đã luôn dành thời gian quý báu để định hướng đề tài, tận tình chỉ bảo, đóng góp những ý kiến phản biện chuyên sâu và kịp thời tháo gỡ những vướng mắc kỹ thuật phức tạp trong suốt quá trình phân tích, thiết kế và lập trình hoàn thiện hệ thống GiaDụngPro.")

    add_para("Chúng em cũng xin gửi lời cảm ơn chân thành đến gia đình, người thân và các bạn bè cùng lớp đã luôn động viên, chia sẻ và hỗ trợ chúng em cả về mặt tinh thần lẫn điều kiện học tập trong suốt quá trình thực hiện đồ án.")

    add_para("Mặc dù nhóm chúng em đã nỗ lực hết mình với tinh thần trách nhiệm cao nhất để nghiên cứu và hoàn thiện hệ thống, song do giới hạn về thời gian và kinh nghiệm thực tế, đề tài khó tránh khỏi những thiếu sót nhất định. Nhóm chúng em rất mong nhận được sự lượng thứ, những lời nhận xét quý báu và góp ý chân thành từ quý Thầy Cô để đồ án ngày càng hoàn thiện hơn và có thể phát triển ứng dụng thực tiễn trong tương lai.")

    add_para("Chúng em xin kính chúc quý Thầy Cô luôn dồi dào sức khỏe, hạnh phúc và luôn gặt hái được nhiều thành công rực rỡ trong sự nghiệp giáo dục cao quý!")

    add_para("Chúng em xin chân thành cảm ơn!", italic=True, align=WD_ALIGN_PARAGRAPH.RIGHT, space_before=15)
    add_para("Sinh viên thực hiện", bold=True, align=WD_ALIGN_PARAGRAPH.RIGHT, space_after=35)
    add_para("Trần Thị Tèo - Trần Thị Tý", bold=True, align=WD_ALIGN_PARAGRAPH.RIGHT)

    doc.add_page_break()

    # =========================================================================
    # 5. LỜI CAM ĐOAN
    # =========================================================================
    add_para("LỜI CAM ĐOAN", bold=True, font_size=16, align=WD_ALIGN_PARAGRAPH.CENTER, space_before=10, space_after=14)

    add_para("Chúng em xin cam đoan đề tài: “Xây dựng website thương mại điện tử bán đồ gia dụng cao cấp - GiaDụngPro” là công trình nghiên cứu và xây dựng độc lập của nhóm sinh viên thực hiện, dưới sự hướng dẫn khoa học trực tiếp của Giảng viên hướng dẫn KS. Huỳnh Luân.")

    add_para("Chúng em xin cam kết các nội dung sau:")
    add_para("1. Toàn bộ mã nguồn chương trình, cấu trúc cơ sở dữ liệu và các tài liệu thiết kế trong đồ án đều do nhóm tự tìm hiểu, phân tích, thiết kế và lập trình thực tế, không sao chép trái phép từ bất kỳ đồ án hoặc mã nguồn của người khác.")
    add_para("2. Các số liệu khảo sát, kết quả kiểm thử và hình ảnh giao diện được trình bày trong báo cáo là hoàn toàn trung thực, phản ánh chính xác kết quả vận hành thực tế của hệ thống trên môi trường máy chủ cục bộ Laragon.")
    add_para("3. Mọi tài liệu tham khảo, thư viện mã nguồn mở và công cụ hỗ trợ được sử dụng trong đề tài đều đã được trích dẫn nguồn gốc rõ ràng và tuân thủ đúng quy định của bản quyền học thuật.")

    add_para("Nếu có bất kỳ sự gian lận hay vi phạm nào về tính trung thực học thuật, nhóm chúng em xin hoàn toàn chịu mọi hình thức kỷ luật theo quy định của Khoa Công nghệ Thông tin và Nhà trường.")

    add_para("TP. Hồ Chí Minh, ngày .... tháng .... năm 2026", italic=True, align=WD_ALIGN_PARAGRAPH.RIGHT, space_before=20)
    add_para("Sinh viên thực hiện", bold=True, align=WD_ALIGN_PARAGRAPH.RIGHT, space_after=35)
    add_para("Trần Thị Tèo - Trần Thị Tý", bold=True, align=WD_ALIGN_PARAGRAPH.RIGHT)

    doc.add_page_break()

    # =========================================================================
    # 6. NHẬN XÉT CỦA GIẢNG VIÊN HƯỚNG DẪN
    # =========================================================================
    add_para("NHẬN XÉT CỦA GIẢNG VIÊN HƯỚNG DẪN", bold=True, font_size=16, align=WD_ALIGN_PARAGRAPH.CENTER, space_before=10, space_after=14)

    add_para("Họ và tên Giảng viên hướng dẫn: KS. Huỳnh Luân")
    add_para("Đơn vị công tác: Khoa Công nghệ Thông tin - Trường Cao đẳng CNTT TP.HCM")
    add_para("Tên đề tài: Xây dựng website thương mại điện tử bán đồ gia dụng cao cấp - GiaDụngPro")
    add_para("Sinh viên thực hiện: Trần Thị Tèo & Trần Thị Tý - Lớp: CD22-CNTT01\n")

    add_para("NỘI DUNG ĐÁNH GIÁ VÀ NHẬN XÉT:", bold=True)
    add_para("1. Về tinh thần, thái độ và tính chủ động của sinh viên trong quá trình thực hiện đồ án:")
    add_para("....................................................................................................................................................")
    add_para("....................................................................................................................................................")

    add_para("2. Về tính khoa học, cấu trúc báo cáo và phương pháp trình bày:")
    add_para("....................................................................................................................................................")
    add_para("....................................................................................................................................................")

    add_para("3. Về chất lượng sản phẩm, mức độ hoàn thiện chức năng và tính ứng dụng thực tiễn:")
    add_para("....................................................................................................................................................")
    add_para("....................................................................................................................................................")

    add_para("4. Những hạn chế cần khắc phục:")
    add_para("....................................................................................................................................................")

    add_para("5. Điểm đánh giá: .........../10 (Bằng chữ: .....................................................................................)")
    add_para("Kết luận: Đồng ý cho sinh viên bảo vệ đồ án trước Hội đồng chấm thi.")

    add_para("TP. Hồ Chí Minh, ngày .... tháng .... năm 2026", italic=True, align=WD_ALIGN_PARAGRAPH.RIGHT, space_before=20)
    add_para("GIẢNG VIÊN HƯỚNG DẪN", bold=True, align=WD_ALIGN_PARAGRAPH.RIGHT, space_after=35)
    add_para("KS. Huỳnh Luân", bold=True, align=WD_ALIGN_PARAGRAPH.RIGHT)

    doc.add_page_break()

    # =========================================================================
    # 7. NHẬN XÉT CỦA GIẢNG VIÊN PHẢN BIỆN
    # =========================================================================
    add_para("NHẬN XÉT CỦA GIẢNG VIÊN PHẢN BIỆN", bold=True, font_size=16, align=WD_ALIGN_PARAGRAPH.CENTER, space_before=10, space_after=14)

    add_para("Họ và tên Giảng viên phản biện: .............................................................................................")
    add_para("Đơn vị công tác: Khoa Công nghệ Thông tin - Trường Cao đẳng CNTT TP.HCM")
    add_para("Tên đề tài: Xây dựng website thương mại điện tử bán đồ gia dụng cao cấp - GiaDụngPro")
    add_para("Sinh viên thực hiện: Trần Thị Tèo & Trần Thị Tý - Lớp: CD22-CNTT01\n")

    add_para("NỘI DUNG ĐÁNH GIÁ:", bold=True)
    add_para("1. Đánh giá tính cấp thiết và giá trị thực tiễn của đề tài:")
    add_para("....................................................................................................................................................")

    add_para("2. Đánh giá kết quả kỹ thuật, kiến trúc hệ thống và cơ sở dữ liệu:")
    add_para("....................................................................................................................................................")

    add_para("3. Câu hỏi phản biện dành cho sinh viên:")
    add_para("• Câu 1: ..........................................................................................................................................")
    add_para("• Câu 2: ..........................................................................................................................................")

    add_para("4. Điểm đánh giá phản biện: .........../10 (Bằng chữ: .................................................................)")

    add_para("TP. Hồ Chí Minh, ngày .... tháng .... năm 2026", italic=True, align=WD_ALIGN_PARAGRAPH.RIGHT, space_before=20)
    add_para("GIẢNG VIÊN PHẢN BIỆN", bold=True, align=WD_ALIGN_PARAGRAPH.RIGHT, space_after=35)
    add_para("(Ký và ghi rõ họ tên)", italic=True, align=WD_ALIGN_PARAGRAPH.RIGHT)

    doc.add_page_break()

    # =========================================================================
    # 8. TÓM TẮT ĐỒ ÁN (ABSTRACT)
    # =========================================================================
    add_para("TÓM TẮT ĐỒ ÁN", bold=True, font_size=16, align=WD_ALIGN_PARAGRAPH.CENTER, space_before=10, space_after=14)

    add_para("1. Vấn đề được đưa ra giải quyết:", bold=True)
    add_para("Xây dựng giải pháp website thương mại điện tử chuyên sâu cho ngành hàng đồ gia dụng gia đình cao cấp, giải quyết bài toán quản lý sản phẩm đa biến thể (màu sắc, kích cỡ, bảo hành), quản lý đơn hàng chuẩn xác, kiểm soát tồn kho chặt chẽ bằng Database Transaction, tích hợp chọn địa chỉ hành chính Việt Nam 3 cấp liên hoàn (Tỉnh - Huyện - Xã) và phân quyền nghiệp vụ rõ ràng giữa Quản trị viên, Quản lý kho, Nhân viên bán hàng và Khách hàng.")

    add_para("2. Mục tiêu đã đạt được:", bold=True)
    add_para("• Thiết kế và cài đặt hoàn chỉnh hệ thống bán lẻ trực tuyến GiaDụngPro với giao diện đẹp mắt, tương thích hoàn hảo đa thiết bị (Desktop, Tablet, Mobile).")
    add_para("• Xây dựng cơ sở dữ liệu quan hệ hoàn chỉnh gồm 47 bảng logic, xử lý mượt mà các nghiệp vụ thương mại điện tử, kiểm soát tồn kho và lịch trực bán hàng.")
    add_para("• Phát triển các tính năng trải nghiệm độc quyền: Giao diện trượt chéo rạp hát (Theatrical Diagonal Sliding Auth), Banner Parallax 3D & hiệu ứng tập trung vật thể, 3D Coverflow Carousel thương hiệu đối tác, Lưới danh mục Tabbed Grid 49 ngành hàng.")
    add_para("• Tích hợp hệ sinh thái Gamification & Loyalty phong phú: Điểm danh nhận quà theo chuỗi ngày, Vòng quay may mắn trúng thưởng, Đổi điểm lấy quà tặng/voucher, Tương tác kết bạn người dùng.")
    add_para("• Trang bị phân hệ Quản trị (Admin Dashboard) mạnh mẽ với tính năng phân công lịch trực nhân viên bán hàng (Sales Schedules), quản lý nhân sự đa quyền hạn và thống kê doanh thu thời gian thực.")

    add_para("3. Kỹ thuật công nghệ sử dụng:", bold=True)
    add_para("• Backend: PHP 8.3+, Laravel 12.x Framework (áp dụng kiến trúc Controller mới implements HasMiddleware, Eloquent ORM, Database Seeder & Migrations).")
    add_para("• Frontend: HTML5, CSS3, Tailwind CSS v4 (thiết lập @custom-variant dark và typography Plus Jakarta Sans), Vanilla JavaScript (ES6, CSS 3D Transforms).")
    add_para("• Bundler: Vite 7.x.")
    add_para("• Cơ sở dữ liệu: MySQL 8.4.3 (hỗ trợ InnoDB Transaction, foreign keys).")
    add_para("• Môi trường và công cụ: Laragon 6.0+, Visual Studio Code, Git, HeidiSQL/phpMyAdmin.")

    add_para("4. Kết quả thực hiện:", bold=True)
    add_para("• Hệ thống vận hành ổn định 100% trên môi trường Laragon Virtual Host (http://giadung_1.test).")
    add_para("• Toàn bộ 18 kịch bản kiểm thử tự động (Automated Tests) với 105 assertions đều đạt chứng nhận PASS, bao gồm luồng mua hàng trọn vẹn E2E (FullE2EPurchasingFlowTest).")

    add_para("5. Kết luận về đồ án:", bold=True)
    add_para("Đồ án đã hoàn thành xuất sắc toàn bộ các mục tiêu đặt ra, có giá trị ứng dụng thực tiễn cao và sẵn sàng đưa vào triển khai kinh doanh thương mại điện tử thực tế.")

    doc.add_page_break()

    # =========================================================================
    # 9. TRANG MỤC LỤC TỔNG QUÁT
    # =========================================================================
    add_para("MỤC LỤC", bold=True, font_size=16, align=WD_ALIGN_PARAGRAPH.CENTER, space_before=10, space_after=14)

    toc_items = [
        ("LỜI MỞ ĐẦU", "1"),
        ("LỜI CẢM ƠN", "2"),
        ("LỜI CAM ĐOAN", "3"),
        ("NHẬN XÉT CỦA GIẢNG VIÊN HƯỚNG DẪN", "4"),
        ("NHẬN XÉT CỦA GIẢNG VIÊN PHẢN BIỆN", "5"),
        ("TÓM TẮT ĐỒ ÁN", "6"),
        ("DANH MỤC CÁC TỪ VIẾT TẮT", "8"),
        ("DANH MỤC BẢNG BIỂU", "9"),
        ("DANH MỤC HÌNH ẢNH & SƠ ĐỒ", "10"),
        ("Chương 1. TỔNG QUAN ĐỀ TÀI VÀ CƠ SỞ LÝ THUYẾT", "11"),
        ("   1.1 Khảo sát bài toán và Thực trạng nghiên cứu", "11"),
        ("      1.1.1 Đặt vấn đề và Tính cấp thiết của đề tài", "11"),
        ("      1.1.2 Mục tiêu nghiên cứu và Phạm vi đề tài", "12"),
        ("      1.1.3 Các đối tượng sử dụng hệ thống", "13"),
        ("   1.2 Cơ sở lý thuyết và Công nghệ sử dụng", "14"),
        ("      1.2.1 Ngôn ngữ lập trình PHP 8.3", "14"),
        ("      1.2.2 Framework Laravel 12 và Kiến trúc hiện đại", "15"),
        ("      1.2.3 Hệ quản trị cơ sở dữ liệu MySQL 8.4", "17"),
        ("      1.2.4 Tailwind CSS v4 và Thiết lập Dark Mode tự động", "18"),
        ("      1.2.5 Vite Bundler và Kỹ thuật tối ưu Frontend", "19"),
        ("      1.2.6 JavaScript ES6, Parallax 3D và CSS 3D Transforms", "20"),
        ("      1.2.7 Môi trường phát triển Laragon và Bộ công cụ hỗ trợ", "21"),
        ("Chương 2. PHÂN TÍCH VÀ THIẾT KẾ HỆ THỐNG", "22"),
        ("   2.1 Phân tích yêu cầu và Quy trình nghiệp vụ", "22"),
        ("      2.1.1 Quy trình mua sắm và đặt hàng trực tuyến", "22"),
        ("      2.1.2 Quy trình xử lý đơn hàng và hoàn trả tồn kho", "23"),
        ("      2.1.3 Quy trình phân công lịch trực bán hàng (Sales Schedules)", "24"),
        ("      2.1.4 Quy trình tích điểm, quay thưởng và đổi quà Gamification", "25"),
        ("      2.1.5 Quy trình tương tác xã hội và đánh giá sản phẩm", "26"),
        ("   2.2 Sơ đồ phân rã chức năng hệ thống (BFD)", "27"),
        ("   2.3 Lược đồ Use Case và Bảng đặc tả Use Case chi tiết", "28"),
        ("      2.3.1 Lược đồ Use Case tổng quát hệ thống", "28"),
        ("      2.3.2 Đặc tả Use Case Đăng nhập và Đăng ký trượt chéo", "29"),
        ("      2.3.3 Đặc tả Use Case Đặt hàng và Thanh toán COD", "30"),
        ("      2.3.4 Đặc tả Use Case Quản lý Đơn hàng và Hoàn kho tự động", "31"),
        ("      2.3.5 Đặc tả Use Case Phân công lịch trực ca bán hàng", "32"),
        ("      2.3.6 Đặc tả Use Case Điểm danh và Quay vòng quay may mắn", "33"),
        ("      2.3.7 Đặc tả Use Case Quản lý tài khoản nhân viên", "34"),
        ("   2.4 Lược đồ hoạt động (Activity Diagrams) cho các luồng then chốt", "35"),
        ("   2.5 Thiết kế Cơ sở dữ liệu (Chi tiết cấu trúc 47 bảng)", "37"),
        ("      2.5.1 Mô hình quan hệ thực thể (ERD)", "37"),
        ("      2.5.2 Nhóm bảng Người dùng và Hệ thống", "38"),
        ("      2.5.3 Nhóm bảng Sản phẩm, Danh mục và Biến thể", "40"),
        ("      2.5.4 Nhóm bảng Giỏ hàng, Đơn hàng và Thanh toán", "44"),
        ("      2.5.5 Nhóm bảng Địa giới hành chính Việt Nam 3 cấp", "46"),
        ("      2.5.6 Nhóm bảng Khuyến mãi, Coupon và Voucher", "47"),
        ("      2.5.7 Nhóm bảng Lịch trực bán hàng (Sales Schedules)", "48"),
        ("      2.5.8 Nhóm bảng Gamification, Tích điểm và Đổi quà", "49"),
        ("      2.5.9 Nhóm bảng Đánh giá, Yêu thích và Tương tác xã hội", "52"),
        ("Chương 3. CÀI ĐẶT THỰC NGHIỆM VÀ KẾT QUẢ ĐẠT ĐƯỢC", "54"),
        ("   3.1 Môi trường cài đặt và Quy trình triển khai", "54"),
        ("   3.2 Giao diện và kết quả thực nghiệm Phân hệ Khách hàng", "55"),
        ("      3.2.1 Giao diện Đăng nhập và Đăng ký trượt chéo (Theatrical Sliding)", "55"),
        ("      3.2.2 Trang chủ với Hero Banner Parallax 3D và Hiệu ứng Focus/Repel", "57"),
        ("      3.2.3 Lưới danh mục phân Tab (Tabbed Grid 49 ngành hàng)", "59"),
        ("      3.2.4 Thương hiệu đối tác 3D Coverflow Carousel", "60"),
        ("      3.2.5 Trang danh sách sản phẩm và Bộ lọc tìm kiếm đa tiêu chí", "61"),
        ("      3.2.6 Trang chi tiết sản phẩm, Bộ chọn biến thể và Thư viện ảnh", "62"),
        ("      3.2.7 Giỏ hàng và Form đặt hàng địa giới hành chính liên hoàn", "64"),
        ("      3.2.8 Trung tâm Gamification: Điểm danh, Vòng quay, Đổi quà", "66"),
        ("      3.2.9 Trang cá nhân, Quản lý đơn hàng và Đánh giá sản phẩm", "68"),
        ("      3.2.10 Giao diện Chế độ tối (Dark Mode) toàn diện", "69"),
        ("   3.3 Giao diện và kết quả thực nghiệm Phân hệ Quản trị (Admin)", "70"),
        ("      3.3.1 Bảng điều khiển tổng quan (Dashboard) và Cảnh báo kho", "70"),
        ("      3.3.2 Phân hệ Quản lý danh mục và Sản phẩm đa biến thể", "71"),
        ("      3.3.3 Phân hệ Quản lý đơn hàng và Cập nhật trạng thái an toàn", "73"),
        ("      3.3.4 Phân hệ Quản lý khách hàng và Kiểm soát tài khoản", "74"),
        ("      3.3.5 Phân hệ Quản lý nhân sự (Employees: Warehouse & Sales)", "75"),
        ("      3.3.6 Phân hệ Lịch trực bán hàng (Sales Schedules Management)", "76"),
        ("   3.4 Kết quả Kiểm thử tự động (Automated Testing)", "78"),
        ("      3.4.1 Cấu hình môi trường kiểm thử với PHPUnit / Pest", "78"),
        ("      3.4.2 Kết quả kiểm thử bộ chức năng E-Commerce", "79"),
        ("      3.4.3 Kết quả kiểm thử bộ chức năng Gamification & Loyalty", "80"),
        ("      3.4.4 Kết quả kiểm thử toàn trình End-to-End (E2E Purchase)", "81"),
        ("Chương 4. KẾT LUẬN VÀ HƯỚNG PHÁT TRIỂN", "82"),
        ("   4.1 Đánh giá kết quả đạt được", "82"),
        ("   4.2 Những hạn chế còn tồn tại", "83"),
        ("   4.3 Hướng nghiên cứu và phát triển tiếp theo", "84"),
        ("TÀI LIỆU THAM KHẢO", "85"),
    ]

    t_toc = doc.add_table(rows=len(toc_items), cols=2)
    t_toc.alignment = WD_TABLE_ALIGNMENT.CENTER
    for idx, (title, page) in enumerate(toc_items):
        r = t_toc.rows[idx]
        cell_l = r.cells[0]
        cell_r = r.cells[1]
        cell_l.paragraphs[0].text = title
        cell_r.paragraphs[0].text = page
        cell_r.paragraphs[0].alignment = WD_ALIGN_PARAGRAPH.RIGHT
        cell_l.paragraphs[0].paragraph_format.space_after = Pt(2)
        cell_r.paragraphs[0].paragraph_format.space_after = Pt(2)
        is_chap = title.startswith("Chương") or title in ["LỜI MỞ ĐẦU", "LỜI CẢM ƠN", "LỜI CAM ĐOAN", "TÓM TẮT ĐỒ ÁN", "TÀI LIỆU THAM KHẢO"]
        for p in [cell_l.paragraphs[0], cell_r.paragraphs[0]]:
            for run in p.runs:
                run.font.name = 'Times New Roman'
                run.font.size = Pt(12)
                if is_chap:
                    run.bold = True

    doc.add_page_break()

    # =========================================================================
    # 10. DANH MỤC CÁC TỪ VIẾT TẮT
    # =========================================================================
    add_para("DANH MỤC CÁC TỪ VIẾT TẮT", bold=True, font_size=16, align=WD_ALIGN_PARAGRAPH.CENTER, space_before=10, space_after=14)

    abbreviations = [
        ("Thuật ngữ viết tắt", "Tên tiếng Anh đầy đủ", "Ý nghĩa giải thích trong đồ án"),
        ("MVC", "Model - View - Controller", "Mô hình kiến trúc phần mềm phân tách dữ liệu, giao diện và logic điều khiển."),
        ("ORM", "Object Relational Mapping", "Kỹ thuật ánh xạ các bảng cơ sở dữ liệu quan hệ sang đối tượng (Eloquent trong Laravel)."),
        ("RDBMS", "Relational Database Management System", "Hệ quản trị cơ sở dữ liệu quan hệ (sử dụng MySQL 8.4)."),
        ("API", "Application Programming Interface", "Giao diện lập trình ứng dụng hỗ trợ trao đổi dữ liệu JSON giữa Client và Server."),
        ("CRUD", "Create - Read - Update - Delete", "Bốn thao tác dữ liệu cơ bản: Thêm, Xem, Sửa, Xóa."),
        ("COD", "Cash On Delivery", "Hình thức thanh toán bằng tiền mặt khi nhận hàng."),
        ("SKU", "Stock Keeping Unit", "Mã đơn vị lưu kho phân loại từng biến thể sản phẩm cụ thể."),
        ("B2C", "Business to Consumer", "Mô hình thương mại điện tử từ doanh nghiệp bán lẻ trực tiếp tới người tiêu dùng."),
        ("UI / UX", "User Interface / User Experience", "Thiết kế giao diện người dùng và Tối ưu trải nghiệm tương tác người dùng."),
        ("BOM", "Byte Order Mark", "Ký tự ẩn UTF-8 ở đầu file mã nguồn có thể gây lỗi cú pháp PHP nếu không xử lý."),
        ("A11y", "Accessibility", "Tiêu chuẩn thiết kế website hỗ trợ tiếp cận thuận tiện cho mọi người dùng."),
        ("E2E", "End to End", "Phương pháp kiểm thử luồng nghiệp vụ toàn trình từ đầu vào đến đầu ra."),
        ("CSRF", "Cross-Site Request Forgery", "Cơ chế bảo vệ chống giả mạo yêu cầu giữa các trang web được tích hợp trong Laravel."),
    ]

    t_abbr = doc.add_table(rows=len(abbreviations), cols=3)
    for r_idx, row in enumerate(abbreviations):
        for c_idx, val in enumerate(row):
            t_abbr.rows[r_idx].cells[c_idx].paragraphs[0].text = val
    format_table(t_abbr, col_widths=[Cm(3.0), Cm(5.5), Cm(8.0)])

    doc.add_page_break()

    # =========================================================================
    # 11. DANH MỤC BẢNG BIỂU & HÌNH ẢNH
    # =========================================================================
    add_para("DANH MỤC BẢNG BIỂU", bold=True, font_size=16, align=WD_ALIGN_PARAGRAPH.CENTER, space_before=10, space_after=14)

    list_tables = [
        ("Bảng 2.1", "Cấu trúc bảng users (Quản lý tài khoản, vai trò và điểm tích lũy)", "38"),
        ("Bảng 2.2", "Cấu trúc bảng categories (Quản lý danh mục sản phẩm)", "40"),
        ("Bảng 2.3", "Cấu trúc bảng products (Thông tin sản phẩm chính và trạng thái kho)", "41"),
        ("Bảng 2.4", "Cấu trúc bảng product_variants (Chi tiết biến thể theo màu sắc và kích cỡ)", "42"),
        ("Bảng 2.5", "Cấu trúc bảng colors, sizes và warranties (Thuộc tính và chính sách bảo hành)", "43"),
        ("Bảng 2.6", "Cấu trúc bảng orders (Quản lý thông tin đơn hàng và giao nhận)", "44"),
        ("Bảng 2.7", "Cấu trúc bảng order_items (Chi tiết mặt hàng và giá lịch sử trong đơn)", "45"),
        ("Bảng 2.8", "Cấu trúc bảng provinces, districts, wards (Địa giới hành chính Việt Nam 3 cấp)", "46"),
        ("Bảng 2.9", "Cấu trúc bảng coupons và vouchers (Quản lý mã giảm giá và voucher cá nhân)", "47"),
        ("Bảng 2.10", "Cấu trúc bảng sales_schedules (Quản lý lịch phân ca trực bán hàng)", "48"),
        ("Bảng 2.11", "Cấu trúc nhóm bảng Gamification (games, daily_checkins, point_transactions)", "49"),
        ("Bảng 2.12", "Cấu trúc bảng reward_items và reward_redemptions (Kho quà và đổi thưởng)", "51"),
        ("Bảng 2.13", "Cấu trúc bảng product_reviews và review_votes (Đánh giá và bầu chọn hữu ích)", "52"),
        ("Bảng 3.1", "Thông số môi trường phần cứng và phần mềm triển khai thực nghiệm", "54"),
        ("Bảng 3.2", "Bảng tổng hợp kết quả chạy bộ kiểm thử tự động 18 kịch bản (105 assertions)", "78"),
    ]

    t_lt = doc.add_table(rows=len(list_tables) + 1, cols=3)
    t_lt.rows[0].cells[0].paragraphs[0].text = "Ký hiệu bảng"
    t_lt.rows[0].cells[1].paragraphs[0].text = "Tên bảng biểu mô tả"
    t_lt.rows[0].cells[2].paragraphs[0].text = "Trang"
    for r_idx, (k, desc, p) in enumerate(list_tables):
        t_lt.rows[r_idx+1].cells[0].paragraphs[0].text = k
        t_lt.rows[r_idx+1].cells[1].paragraphs[0].text = desc
        t_lt.rows[r_idx+1].cells[2].paragraphs[0].text = p
        t_lt.rows[r_idx+1].cells[2].paragraphs[0].alignment = WD_ALIGN_PARAGRAPH.RIGHT
    format_table(t_lt, col_widths=[Cm(3.0), Cm(11.0), Cm(2.0)])

    add_para("", space_after=15)
    add_para("DANH MỤC HÌNH ẢNH & SƠ ĐỒ", bold=True, font_size=16, align=WD_ALIGN_PARAGRAPH.CENTER, space_before=15, space_after=14)

    list_figures = [
        ("Sơ đồ 2.1", "Sơ đồ phân rã chức năng hệ thống GiaDụngPro (BFD)", "27"),
        ("Lược đồ 2.1", "Lược đồ Use Case tổng quát của hệ thống", "28"),
        ("Lược đồ 2.2", "Lược đồ hoạt động (Activity Diagram) Quy trình đặt hàng và trừ tồn kho", "35"),
        ("Lược đồ 2.3", "Lược đồ hoạt động Điểm danh chuỗi ngày và Quay thưởng may mắn", "36"),
        ("Hình 2.1", "Mô hình quan hệ thực thể (ERD) toàn diện của 47 bảng CSDL", "37"),
        ("Hình 3.1", "Giao diện Đăng nhập & Đăng ký trượt chéo (Theatrical Diagonal Sliding)", "56"),
        ("Hình 3.2", "Giao diện Hero Banner V2 với hiệu ứng Parallax 3D và Tập trung vật thể", "58"),
        ("Hình 3.3", "Lưới danh mục sản phẩm phân Tab 49 ngành hàng gia dụng", "59"),
        ("Hình 3.4", "Thương hiệu đối tác trình diễn dạng 3D Coverflow Carousel", "60"),
        ("Hình 3.5", "Trang chi tiết sản phẩm với bộ chọn biến thể Màu sắc, Kích cỡ và Bảo hành", "63"),
        ("Hình 3.6", "Giao diện Giỏ hàng và Form thanh toán liên hoàn Tỉnh - Huyện - Xã", "65"),
        ("Hình 3.7", "Trung tâm Gamification: Vòng quay may mắn và Bảng quà tặng tích điểm", "67"),
        ("Hình 3.8", "Giao diện Bảng điều khiển Quản trị viên (Admin Dashboard)", "70"),
        ("Hình 3.9", "Phân hệ Quản lý Lịch trực bán hàng (Sales Schedules Management)", "76"),
        ("Hình 3.10", "Kết quả thực thi bộ kiểm thử tự động 18 tests passed trên Terminal", "81"),
    ]

    t_lf = doc.add_table(rows=len(list_figures) + 1, cols=3)
    t_lf.rows[0].cells[0].paragraphs[0].text = "Ký hiệu hình"
    t_lf.rows[0].cells[1].paragraphs[0].text = "Tên hình ảnh / sơ đồ minh họa"
    t_lf.rows[0].cells[2].paragraphs[0].text = "Trang"
    for r_idx, (k, desc, p) in enumerate(list_figures):
        t_lf.rows[r_idx+1].cells[0].paragraphs[0].text = k
        t_lf.rows[r_idx+1].cells[1].paragraphs[0].text = desc
        t_lf.rows[r_idx+1].cells[2].paragraphs[0].text = p
        t_lf.rows[r_idx+1].cells[2].paragraphs[0].alignment = WD_ALIGN_PARAGRAPH.RIGHT
    format_table(t_lf, col_widths=[Cm(3.0), Cm(11.0), Cm(2.0)])

    doc.add_page_break()

    # =========================================================================
    # SETUP HEADER & FOOTER FOR MAIN CONTENT (Starts numbering from Chapter 1)
    # =========================================================================
    # We add a new section for the main body
    body_section = doc.add_section()
    body_section.top_margin = Cm(2.0)
    body_section.bottom_margin = Cm(2.0)
    body_section.left_margin = Cm(3.0)
    body_section.right_margin = Cm(2.0)

    # Header
    header = body_section.header
    header.is_linked_to_previous = False
    p_hdr = header.paragraphs[0]
    p_hdr.text = "Đồ án môn học: Website Bán Đồ Gia Dụng Cao Cấp GiaDụngPro\tGVHD: KS. Huỳnh Luân"
    p_hdr.alignment = WD_ALIGN_PARAGRAPH.LEFT
    for run in p_hdr.runs:
        run.font.name = 'Times New Roman'
        run.font.size = Pt(9.5)
        run.italic = True
        run.font.color.rgb = RGBColor(100, 116, 139)

    # Footer
    footer = body_section.footer
    footer.is_linked_to_previous = False
    p_ftr = footer.paragraphs[0]
    p_ftr.text = "SVTH: Trần Thị Tèo & Trần Thị Tý - Lớp: CD22-CNTT01\tTrang "
    p_ftr.alignment = WD_ALIGN_PARAGRAPH.LEFT
    for run in p_ftr.runs:
        run.font.name = 'Times New Roman'
        run.font.size = Pt(9.5)
        run.italic = True
        run.font.color.rgb = RGBColor(100, 116, 139)
    # Add page field to footer
    fldSimple = parse_xml(r'<w:fldSimple %s w:instr="PAGE"/>' % nsdecls('w'))
    p_ftr._p.append(fldSimple)

    # =========================================================================
    # CHƯƠNG 1: TỔNG QUAN ĐỀ TÀI VÀ CƠ SỞ LÝ THUYẾT
    # =========================================================================
    add_heading_1("Chương 1. TỔNG QUAN ĐỀ TÀI VÀ CƠ SỞ LÝ THUYẾT")

    add_heading_2("1.1 Khảo sát bài toán và Thực trạng nghiên cứu")

    add_heading_3("1.1.1 Đặt vấn đề và Tính cấp thiết của đề tài")
    add_para("Trong bối cảnh nền kinh tế số và lối sống hiện đại tại Việt Nam, các thiết bị gia dụng nhà bếp và chăm sóc gia đình (như nồi chiên không dầu điện tử, chảo inox đúc liền khối, máy xay sinh tố công suất cao, nồi cơm điện cao tần IH, robot hút bụi lau nhà, máy lọc nước và máy tạo ion làm mát không khí) đã trở thành nhu cầu thiết yếu đối với mỗi gia đình. Người tiêu dùng ngày nay không chỉ đơn thuần tìm kiếm những vật dụng nấu nướng cơ bản, mà hướng tới những sản phẩm cao cấp, an toàn cho sức khỏe, có độ bền vượt trội và sở hữu phong cách thiết kế sang trọng, hài hòa với không gian nội thất.")

    add_para("Mặc dù các sàn thương mại điện tử lớn (Shopee, Lazada, Tiki) đã rất phổ biến, song việc kinh doanh ngành hàng gia dụng cao cấp qua các sàn tổng hợp này lại bộc lộ nhiều điểm nghẽn nghiêm trọng:")
    add_para("1. Trải nghiệm thị giác nghèo nàn: Giao diện các sàn tổng hợp bị phân mảnh bởi hàng triệu mặt hàng vụn vặt, thiếu hẳn không gian chuyên nghiệp để trình diễn vẻ đẹp sang trọng, đẳng cấp của các thiết bị gia dụng chính hãng.")
    add_para("2. Hạn chế về quản lý cấu hình biến thể sản phẩm: Thiết bị gia dụng thường có nhiều phiên bản công suất, màu sắc đặc trưng (như Đen nhám, Bạc kim loại, Đỏ ruby) và các gói gia hạn bảo hành chính hãng (12 tháng, 24 tháng, đổi mới 1-1). Các hệ thống cũ thường tách rời thành nhiều sản phẩm riêng rẽ gây rối mắt và làm phân tán lượt đánh giá của khách hàng.")
    add_para("3. Tỷ lệ hủy đơn và sai lệch địa chỉ giao hàng cao: Địa giới hành chính Việt Nam rất đặc thù với 3 cấp (Tỉnh/Thành phố, Quận/Huyện, Phường/Xã). Việc thiếu cơ chế tải dữ liệu địa lý phân cấp tự động (Cascading Dropdown) khiến người mua dễ gõ sai địa chỉ, gây chậm trễ trong quá trình giao nhận.")
    add_para("4. Khó khăn trong quản lý nhân sự và điều phối ca trực bán hàng: Hầu hết các website bán hàng truyền thống chỉ có quyền Admin duy nhất, không phân chia quyền hạn cho Nhân viên kho (Warehouse) hay Nhân viên tư vấn bán hàng (Sales), đồng thời thiếu công cụ phân ca trực và giám sát năng suất làm việc của nhân sự.")

    add_heading_3("1.1.2 Mục tiêu nghiên cứu và Phạm vi đề tài")
    add_para("Nhằm giải quyết triệt để các tồn tại trên, mục tiêu trọng tâm của đề tài là xây dựng một nền tảng thương mại điện tử chuyên nghiệp, toàn diện mang tên GiaDụngPro, đáp ứng các tiêu chuẩn khắt khe sau:")
    add_para("• Về mặt kiến trúc và công nghệ: Ứng dụng phiên bản mới nhất của Framework PHP là Laravel 12 (phát hành đầu năm 2025/2026), tận dụng triệt để kiến trúc HasMiddleware mới, Eloquent ORM và mô hình kiểm soát giao dịch cơ sở dữ liệu nghiêm ngặt.")
    add_para("• Về mặt trải nghiệm người dùng (UX/UI): Tiên phong ứng dụng các hiệu ứng giao diện hiện đại hàng đầu như: Form Đăng nhập/Đăng ký trượt chéo rạp hát (Theatrical Diagonal Sliding Auth), Banner Parallax 3D bám tọa độ chuột kèm hiệu ứng đẩy tách không gian độc quyền (Focus & Repel), 3D Coverflow Carousel giới thiệu thương hiệu và Lưới phân tab 49 ngành hàng tức thời.")
    add_para("• Về mặt nghiệp vụ thương mại: Quản lý chi tiết biến thể sản phẩm (Variant Matrix), giỏ hàng đồng bộ đa cơ chế (Session kết hợp Database), thanh toán đơn hàng an toàn với địa chỉ hành chính Việt Nam 3 cấp, áp dụng Coupon/Voucher thông minh và tự động hoàn trả tồn kho khi đơn hàng bị hủy.")
    add_para("• Về mặt gắn kết khách hàng (Loyalty & Gamification): Tích hợp chuỗi điểm danh hàng ngày nhận điểm thưởng, Vòng quay may mắn (Lucky Wheel), Đổi điểm lấy quà tặng thực tế và Mạng xã hội kết bạn người dùng.")
    add_para("• Về mặt quản trị và vận hành: Phân quyền chặt chẽ 4 vai trò (Quản trị viên, Nhân viên kho, Nhân viên bán hàng, Khách hàng) và tích hợp hệ thống phân công lịch trực bán hàng (Sales Schedules) linh hoạt theo ngày/tháng.")

    add_heading_3("1.1.3 Các đối tượng sử dụng hệ thống")
    add_para("Hệ thống GiaDụngPro phục vụ 4 nhóm đối tượng tác nhân (Actors) chính:")
    add_para("1. Khách vãng lai (Guest): Khách hàng chưa đăng nhập tài khoản. Có thể tìm kiếm, lọc sản phẩm, xem chi tiết và thêm sản phẩm vào giỏ hàng tạm thời (lưu trên Session).")
    add_para("2. Khách hàng thành viên (Customer): Khách hàng đã đăng ký và kích hoạt tài khoản. Được hưởng các đặc quyền: lưu đơn hàng, đồng bộ giỏ hàng vĩnh viễn, lưu danh sách yêu thích (Wishlist), điểm danh nhận điểm thưởng, quay vòng quay may mắn, đổi điểm lấy voucher và tham gia đánh giá nhận xét sản phẩm.")
    add_para("3. Nhân viên bán hàng (Sales Staff): Tài khoản nhân viên có vai trò 'sales'. Có quyền truy cập khu vực quản lý lịch trực cá nhân, tiếp nhận ca trực được phân công và cập nhật trạng thái hoàn thành ca làm việc.")
    add_para("4. Nhân viên quản lý kho (Warehouse Staff): Tài khoản nhân viên có vai trò 'warehouse'. Có quyền truy cập vào danh mục sản phẩm, cập nhật số lượng tồn kho, chỉnh sửa thông tin kỹ thuật sản phẩm và điều chỉnh trạng thái danh mục hàng hóa.")
    add_para("5. Quản trị viên tối cao (Super Admin): Nắm toàn quyền điều hành toàn bộ hệ thống: xem bảng điều khiển doanh thu tổng quan, quản lý danh mục, sản phẩm, đơn hàng, khách hàng, tạo tài khoản nhân viên, phân quyền và điều phối lịch trực bán hàng.")

    add_heading_2("1.2 Cơ sở lý thuyết và Công nghệ sử dụng")

    add_heading_3("1.2.1 Ngôn ngữ lập trình PHP 8.3")
    add_para("PHP (Hypertext Preprocessor) là ngôn ngữ lập trình kịch bản máy chủ (Server-side Scripting) mã nguồn mở phổ biến nhất trên thế giới, chiếm hơn 75% thị phần các website có máy chủ backend hiện nay. Phiên bản PHP 8.3 mang lại những cải tiến vượt trội về hiệu năng thực thi nhờ bộ biên dịch JIT (Just-In-Time) nâng cao, khả năng định kiểu dữ liệu nghiêm ngặt (Strict Types), thuộc tính Readonly Class, Typed Class Constants và cơ chế xử lý ngoại lệ an toàn, tạo nền tảng vững chắc để xây dựng các ứng dụng web quy mô lớn.")

    add_heading_3("1.2.2 Framework Laravel 12 và Kiến trúc hiện đại")
    add_para("Laravel là PHP Framework dẫn đầu thế giới hiện nay nhờ cú pháp thanh lịch, biểu cảm (Expressive Syntax) và hệ sinh thái công cụ hỗ trợ phát triển mạnh mẽ. Dự án GiaDụngPro được xây dựng trên phiên bản Laravel 12.64.0 mới nhất, sở hữu các đặc tính kiến trúc tiên tiến:")
    add_para("• Kiến trúc MVC (Model - View - Controller): Phân tách rạch ròi giữa tầng dữ liệu (Eloquent Models trong app/Models), tầng giao diện người dùng (Blade Views trong resources/views) và tầng xử lý nghiệp vụ (Controllers trong app/Http/Controllers).")
    add_para("• Cơ chế Middleware mới (HasMiddleware Interface): Trong Laravel 11 và 12, lớp Controller cơ sở đã loại bỏ hoàn toàn phương thức $this->middleware(...) cũ trong hàm __construct(). Thay vào đó, Laravel 12 áp dụng cơ chế định nghĩa middleware tĩnh thông qua interface Illuminate\\Routing\\Controllers\\HasMiddleware và phương thức public static function middleware(): array. Nhóm nghiên cứu đã nắm bắt và chuẩn hóa toàn bộ các controller trong dự án theo chuẩn mới này, đảm bảo tính ổn định và tương thích lâu dài.")
    add_para("• Eloquent ORM: Công cụ ánh xạ quan hệ đối tượng trực quan, hỗ trợ thiết lập các quan hệ phức tạp: hasMany, belongsTo, hasOne, belongsToMany kèm cơ chế Eager Loading (with) để loại bỏ hoàn toàn lỗi truy vấn N+1 Queries.")
    add_para("• Database Migrations & Seeders: Cho phép phiên bản hóa cấu trúc cơ sở dữ liệu bằng mã PHP, giúp quá trình triển khai hệ thống trở nên nhất quán và tự động hóa tuyệt đối.")

    add_heading_3("1.2.3 Hệ quản trị cơ sở dữ liệu MySQL 8.4")
    add_para("MySQL là hệ quản trị cơ sở dữ liệu quan hệ (RDBMS) hàng đầu thế giới với độ tin cậy cao, tốc độ xử lý nhanh và tính năng bảo mật mạnh mẽ. Phiên bản MySQL 8.4 hỗ trợ lưu trữ chuẩn bảng mã UTF8MB4 (hỗ trợ đầy đủ tiếng Việt có dấu và biểu tượng cảm xúc Emoji), engine InnoDB với hỗ trợ khóa dòng (Row-level Locking) và cơ chế giao dịch ACID (Atomicity, Consistency, Isolation, Durability) chuẩn mực. Điều này đảm bảo rằng các thao tác đặt hàng, trừ tồn kho và hoàn kho trong GiaDụngPro diễn ra an toàn tuyệt đối, không bao giờ xảy ra tình trạng bán vượt quá số lượng tồn thực tế (Overselling).")

    add_heading_3("1.2.4 Tailwind CSS v4 và Thiết lập Dark Mode tự động")
    add_para("Tailwind CSS v4 là bước đột phá lớn của framework CSS Utility-first, loại bỏ file cấu hình tailwind.config.js rườm rà và chuyển toàn bộ thiết lập sang cú pháp CSS hiện đại (@theme, @custom-variant). Dự án tận dụng tối đa Tailwind CSS v4 để xây dựng hệ thống Design System hoàn chỉnh:")
    add_para("• Kiểu chữ chuẩn quốc tế Plus Jakarta Sans: Tối ưu khả năng đọc và mang lại nét thanh lịch, hiện đại cho toàn bộ giao diện.")
    add_para("• Chế độ Dark Mode linh hoạt: Sử dụng directive @custom-variant dark (&:where(.dark, .dark *)) kết hợp lưu trạng thái chủ đề vào Cookie người dùng, giúp website tự động ghi nhớ và chuyển đổi mượt mà giữa hai giao diện Sáng / Tối.")

    add_heading_3("1.2.5 Vite 7.x và Kỹ thuật tối ưu Frontend")
    add_para("Vite là công cụ đóng gói và xây dựng tài nguyên Frontend thế hệ mới, sở hữu tốc độ khởi động tức thì và khả năng Hot Module Replacement (HMR) tính bằng mili-giây. Dự án sử dụng Vite 7.x kết hợp plugin Laravel-Vite để tự động nén mã nguồn (Minification), phân tách mã (Code Splitting) và quản lý tài nguyên tĩnh một cách tối ưu.")

    add_heading_3("1.2.6 JavaScript ES6, Parallax 3D và CSS 3D Transforms")
    add_para("Toàn bộ các chuyển động và hoạt họa tương tác cao cấp trên website đều được xây dựng bằng Vanilla JavaScript (ES6+) thuần túy, không phụ thuộc vào các thư viện nặng nề như jQuery, giúp tốc độ tải trang đạt mức tối đa:")
    add_para("• Hiệu ứng Parallax 3D: Tính toán độ lệch tọa độ chuột thời gian thực thông qua sự kiện mousemove để tạo chiều sâu không gian sống động cho Banner căn bếp.")
    add_para("• Phép biến đổi hình học 3D (CSS 3D Transforms): Ứng dụng các hàm phối cảnh perspective, translateZ, rotateY, scale để tạo nên vòng xoay thương hiệu 3D Coverflow mượt mà 60 FPS.")

    add_heading_3("1.2.7 Môi trường phát triển Laragon và Bộ công cụ hỗ trợ")
    add_para("Hệ thống được phát triển và kiểm thử thực tế trên nền tảng Laragon 6.0+ (Windows 10/11) - một môi trường WAMP cô lập, tốc độ cao và cực kỳ thân thiện:")
    add_para("• Cơ chế Virtual Host tự động: Tạo tên miền ảo http://giadung_1.test trỏ thẳng vào thư mục public của Laravel mà không cần chỉnh sửa file hosts thủ công.")
    add_para("• Tích hợp đầy đủ công cụ: PHP 8.3.30, MySQL 8.4.3, Node.js v24.15.0, npm 11.12.1, Git và phpMyAdmin 5.2.")

    doc.add_page_break()

    # =========================================================================
    # CHƯƠNG 2: PHÂN TÍCH VÀ THIẾT KẾ HỆ THỐNG
    # =========================================================================
    add_heading_1("Chương 2. PHÂN TÍCH VÀ THIẾT KẾ HỆ THỐNG")

    add_heading_2("2.1 Phân tích yêu cầu và Quy trình nghiệp vụ")

    add_heading_3("2.1.1 Quy trình mua sắm và đặt hàng trực tuyến")
    add_para("Quy trình mua hàng là huyết mạch cốt lõi của website thương mại điện tử GiaDụngPro, được thiết kế tối giản nhằm tối đa hóa tỷ lệ chuyển đổi (Conversion Rate):")
    add_para("1. Tìm kiếm và Lựa chọn sản phẩm: Khách hàng tìm kiếm theo từ khóa hoặc lọc theo danh mục, khoảng giá và thương hiệu. Tại trang chi tiết sản phẩm, khách hàng lựa chọn màu sắc, kích cỡ và gói bảo hành mong muốn. Hệ thống sẽ tự động gọi AJAX để cập nhật giá tiền, mã SKU và số lượng tồn kho tương ứng của biến thể.")
    add_para("2. Quản lý giỏ hàng: Khách hàng bấm 'Thêm vào giỏ hàng'. Hệ thống kiểm tra số lượng yêu cầu với số lượng tồn kho thực tế của biến thể đó. Nếu khách hàng chưa đăng nhập, giỏ hàng được lưu trữ trong Session; khi khách hàng đăng nhập, hệ thống sẽ tự động hợp nhất (Merge) giỏ hàng từ Session vào cơ sở dữ liệu.")
    add_para("3. Tiến hành thanh toán (Checkout): Khách hàng điền thông tin người nhận, chọn Tỉnh/Thành phố -> Quận/Huyện -> Phường/Xã từ hệ thống dữ liệu phân cấp 3 cấp chuẩn. Khách hàng có thể nhập mã giảm giá Coupon hoặc chọn Voucher cá nhân tích lũy từ vòng quay may mắn để được chiết khấu trực tiếp.")
    add_para("4. Khởi tạo đơn hàng an toàn (Database Transaction): Hệ thống bắt đầu một giao dịch DB Transaction: tạo bản ghi trong bảng orders, ghi nhận chi tiết từng món hàng vào order_items kèm giá bán lịch sử (Historical Price), trừ số lượng tồn kho của biến thể sản phẩm, xóa sạch giỏ hàng và chuyển hướng khách hàng đến trang chi tiết đơn hàng thành công.")

    add_heading_3("2.1.2 Quy trình xử lý đơn hàng và hoàn trả tồn kho")
    add_para("Để đảm bảo tính toàn vẹn dữ liệu, quy trình thay đổi trạng thái đơn hàng được kiểm soát nghiêm ngặt:")
    add_para("• Vòng đời trạng thái đơn hàng gồm các bước tuần tự: 'pending' (Chờ xử lý) -> 'processing' (Đang đóng gói) -> 'shipping' (Đang vận chuyển) -> 'completed' (Đã giao hàng & hoàn thành) hoặc 'cancelled' (Đã hủy).")
    add_para("• Cơ chế hoàn kho tự động: Khách hàng có thể tự hủy đơn khi đơn hàng đang ở trạng thái 'pending'. Quản trị viên cũng có thể hủy đơn hàng từ trang quản trị. Ngay khi sự kiện hủy đơn xảy ra, hệ thống sẽ kích hoạt logic hoàn trả lại toàn bộ số lượng sản phẩm tương ứng vào kho hàng, đảm bảo số liệu tồn kho luôn khớp đúng 100%.")

    add_heading_3("2.1.3 Quy trình phân công lịch trực bán hàng (Sales Schedules)")
    add_para("GiaDụngPro tiên phong số hóa quy trình quản lý ca trực của nhân viên tư vấn bán hàng showroom/online:")
    add_para("1. Phân công ca trực: Quản trị viên truy cập phân hệ Lịch trực bán hàng (/admin/sales-schedules), chọn nhân viên bán hàng (User có role='sales'), chọn ngày làm việc, ca trực (Ca Sáng: 8h-12h, Ca Chiều: 13h-17h, Ca Tối: 18h-22h) và nhập ghi chú công việc.")
    add_para("2. Tiếp nhận và Thực hiện: Khi nhân viên bán hàng đăng nhập vào hệ thống, họ chỉ nhìn thấy các ca trực được phân công cho chính mình. Nhân viên xem lịch theo tuần hoặc tháng để chủ động sắp xếp thời gian.")
    add_para("3. Cập nhật tiến độ: Sau khi hoàn thành ca làm việc, nhân viên cập nhật trạng thái ca trực từ 'assigned' (Đã phân công) sang 'completed' (Đã hoàn thành) kèm báo cáo tóm tắt.")

    add_heading_3("2.1.4 Quy trình tích điểm, quay thưởng và đổi quà Gamification")
    add_para("Hệ thống chăm sóc khách hàng thân thiết được thiết kế theo cơ chế Gamification hấp dẫn:")
    add_para("• Điểm danh hàng ngày (Daily Check-in): Mỗi ngày đăng nhập và bấm điểm danh, khách hàng nhận được 10 điểm. Nếu điểm danh liên tục đạt các mốc chuỗi 7 ngày, 14 ngày hoặc 30 ngày, hệ thống sẽ tự động nhân đôi và cộng điểm thưởng lũy tiến.")
    add_para("• Vòng quay may mắn (Lucky Wheel): Khách hàng sử dụng điểm tích lũy để quay thưởng (tiêu tốn 20 điểm/lượt). Phần thưởng ngẫu nhiên bao gồm: Điểm thưởng thêm, Voucher giảm giá 50.000đ, Voucher 100.000đ hoặc quà tặng hiện vật.")
    add_para("• Đổi quà tích điểm: Khách hàng tích lũy đủ điểm có thể truy cập kho quà để đổi lấy các vật phẩm gia dụng nhỏ (ấm đun siêu tốc mini, bộ dao làm bếp) hoặc mã giảm giá độc quyền.")

    add_heading_3("2.1.5 Quy trình tương tác xã hội và đánh giá sản phẩm")
    add_para("Khách hàng có thể gửi lời mời kết bạn với các thành viên khác trên hệ thống, theo dõi danh sách sản phẩm bạn bè đã mua hoặc đánh giá. Khi mua xong sản phẩm, khách hàng có quyền viết nhận xét, chấm điểm sao và tải ảnh thực tế. Những người dùng khác có thể bấm bình chọn 'Hữu ích' (Upvote) cho nhận xét đó.")

    add_heading_2("2.2 Sơ đồ phân rã chức năng hệ thống (BFD)")
    add_para("Sơ đồ 2.1 dưới đây thể hiện cấu trúc phân rã chức năng tổng thể của website GiaDụngPro thành 4 phân hệ chính:")

    t_bfd = doc.add_table(rows=5, cols=4)
    t_bfd.rows[0].cells[0].paragraphs[0].text = "1. Phân hệ Khách hàng"
    t_bfd.rows[0].cells[1].paragraphs[0].text = "2. Phân hệ Gamification"
    t_bfd.rows[0].cells[2].paragraphs[0].text = "3. Phân hệ Nhân viên"
    t_bfd.rows[0].cells[3].paragraphs[0].text = "4. Phân hệ Quản trị (Admin)"

    t_bfd.rows[1].cells[0].paragraphs[0].text = "• Đăng ký / Đăng nhập trượt chéo\n• Xem trang chủ Parallax 3D\n• Tìm kiếm & lọc sản phẩm\n• Xem biến thể & gallery ảnh\n• Giỏ hàng & Đặt hàng COD\n• Tra cứu lịch sử đơn hàng"
    t_bfd.rows[1].cells[1].paragraphs[0].text = "• Điểm danh theo chuỗi ngày\n• Vòng quay may mắn đổi điểm\n• Đổi quà & Voucher giảm giá\n• Kết bạn & Tương tác xã hội\n• Đánh giá & Vote nhận xét"
    t_bfd.rows[1].cells[2].paragraphs[0].text = "• Nhân viên kho: Cập nhật tồn kho, danh mục, biến thể sản phẩm.\n• Nhân viên bán hàng: Xem ca trực cá nhân, báo cáo hoàn thành ca làm việc."
    t_bfd.rows[1].cells[3].paragraphs[0].text = "• Dashboard thống kê doanh thu\n• Quản lý danh mục & sản phẩm\n• Quản lý đơn hàng & hoàn kho\n• Quản lý tài khoản khách hàng\n• Quản lý nhân sự đa quyền\n• Phân công ca trực bán hàng"

    for c in t_bfd.rows[0].cells:
        p = c.paragraphs[0]
        p.alignment = WD_ALIGN_PARAGRAPH.CENTER
        for r in p.runs:
            r.bold = True
    format_table(t_bfd, col_widths=[Cm(4.0), Cm(4.0), Cm(4.0), Cm(4.0)])
    add_para("Sơ đồ 2.1: Sơ đồ phân rã chức năng tổng quát hệ thống GiaDụngPro", italic=True, align=WD_ALIGN_PARAGRAPH.CENTER, space_before=4, space_after=14)

    add_heading_2("2.3 Lược đồ Use Case và Bảng đặc tả Use Case chi tiết")

    add_heading_3("2.3.1 Lược đồ Use Case tổng quát")
    add_para("Lược đồ Use Case tổng quát mô tả sự tương tác giữa 4 tác nhân chính (Khách hàng, Nhân viên bán hàng, Nhân viên kho, Quản trị viên) với các nhóm chức năng cốt lõi của hệ thống:")
    add_para("• Tác nhân Khách hàng (Customer): Thực hiện các Use Case Đăng ký, Đăng nhập, Duyệt sản phẩm, Thêm giỏ hàng, Đặt hàng, Hủy đơn, Điểm danh, Quay thưởng, Đánh giá sản phẩm.")
    add_para("• Tác nhân Nhân viên kho (Warehouse Staff): Thực hiện Use Case Xem danh sách sản phẩm, Cập nhật tồn kho, Thêm/sửa thông tin kỹ thuật sản phẩm, Quản lý danh mục.")
    add_para("• Tác nhân Nhân viên bán hàng (Sales Staff): Thực hiện Use Case Xem lịch trực được phân công, Cập nhật trạng thái hoàn thành ca trực.")
    add_para("• Tác nhân Quản trị viên (Admin): Toàn quyền thực hiện các Use Case Quản trị Dashboard, Quản lý đơn hàng, Quản lý khách hàng, Quản lý nhân viên, Phân công ca trực bán hàng.")

    add_heading_3("2.3.2 Bảng đặc tả Use Case chi tiết")

    # UC 1
    add_heading_4("Bảng đặc tả Use Case UC-01: Đăng nhập và Đăng ký trượt chéo")
    t_uc1 = doc.add_table(rows=8, cols=2)
    t_uc1.rows[0].cells[0].paragraphs[0].text = "Mã Use Case / Tên"
    t_uc1.rows[0].cells[1].paragraphs[0].text = "UC-01: Đăng nhập & Đăng ký tài khoản (Theatrical Sliding Auth)"
    t_uc1.rows[1].cells[0].paragraphs[0].text = "Tác nhân (Actor)"
    t_uc1.rows[1].cells[1].paragraphs[0].text = "Khách vãng lai (Guest), Khách hàng thành viên (Customer), Nhân sự"
    t_uc1.rows[2].cells[0].paragraphs[0].text = "Mô tả tóm tắt"
    t_uc1.rows[2].cells[1].paragraphs[0].text = "Người dùng thực hiện đăng nhập hoặc đăng ký tài khoản trên cùng một khung giao diện chuyển cảnh trượt chéo rạp hát không reload trang."
    t_uc1.rows[3].cells[0].paragraphs[0].text = "Điều kiện tiên quyết"
    t_uc1.rows[3].cells[1].paragraphs[0].text = "Người dùng truy cập vào đường dẫn /login hoặc /register."
    t_uc1.rows[4].cells[0].paragraphs[0].text = "Luồng sự kiện chính (Hành động tác nhân)"
    t_uc1.rows[4].cells[1].paragraphs[0].text = "1. Người dùng bấm chọn tab Đăng nhập hoặc Đăng ký.\n2. Mặt nạ Onyx quét nghiêng chuyển đổi form tương ứng.\n3. Người dùng nhập thông tin (Email, Mật khẩu, Họ tên, SĐT).\n4. Bấm nút gửi thông tin form."
    t_uc1.rows[5].cells[0].paragraphs[0].text = "Phản ứng của hệ thống"
    t_uc1.rows[5].cells[1].paragraphs[0].text = "1. Kiểm tra tính hợp lệ dữ liệu (Validation Rules).\n2. Nếu đăng nhập: Đối chiếu mật khẩu băm Bcrypt, cấp phiên Session, hợp nhất giỏ hàng tạm vào database và chuyển hướng theo vai trò (Admin vào /admin, User về /).\n3. Nếu đăng ký: Khởi tạo bản ghi User mới với role='user', tặng ngay 100 điểm thưởng chào mừng."
    t_uc1.rows[6].cells[0].paragraphs[0].text = "Luồng ngoại lệ"
    t_uc1.rows[6].cells[1].paragraphs[0].text = "Nếu sai mật khẩu hoặc email đã tồn tại: Hệ thống hiển thị thông báo lỗi trực quan mà không làm mất trạng thái form."
    t_uc1.rows[7].cells[0].paragraphs[0].text = "Điều kiện sau khi thực hiện"
    t_uc1.rows[7].cells[1].paragraphs[0].text = "Người dùng đã được xác thực danh tính và cập nhật trạng thái trên thanh điều hướng Navbar."
    format_table(t_uc1, col_widths=[Cm(4.5), Cm(11.5)])

    add_para("", space_after=10)

    # UC 2
    add_heading_4("Bảng đặc tả Use Case UC-02: Đặt hàng và Thanh toán COD")
    t_uc2 = doc.add_table(rows=7, cols=2)
    t_uc2.rows[0].cells[0].paragraphs[0].text = "Mã Use Case / Tên"
    t_uc2.rows[0].cells[1].paragraphs[0].text = "UC-02: Đặt hàng và Thanh toán địa giới 3 cấp"
    t_uc2.rows[1].cells[0].paragraphs[0].text = "Tác nhân (Actor)"
    t_uc2.rows[1].cells[1].paragraphs[0].text = "Khách hàng (Customer)"
    t_uc2.rows[2].cells[0].paragraphs[0].text = "Điều kiện tiên quyết"
    t_uc2.rows[2].cells[1].paragraphs[0].text = "Giỏ hàng có ít nhất một sản phẩm còn hàng tồn kho."
    t_uc2.rows[3].cells[0].paragraphs[0].text = "Luồng sự kiện chính"
    t_uc2.rows[3].cells[1].paragraphs[0].text = "1. Khách hàng vào trang Giỏ hàng bấm 'Tiến hành đặt hàng'.\n2. Chọn Tỉnh/Thành phố -> Hệ thống tải Quận/Huyện -> Chọn Quận/Huyện -> Hệ thống tải Phường/Xã tương ứng.\n3. Điền địa chỉ số nhà, họ tên người nhận và số điện thoại.\n4. Nhập mã giảm giá Coupon hoặc chọn Voucher (nếu có).\n5. Bấm nút 'Xác nhận đặt hàng'."
    t_uc3 = t_uc2
    t_uc3.rows[4].cells[0].paragraphs[0].text = "Phản ứng của hệ thống"
    t_uc3.rows[4].cells[1].paragraphs[0].text = "1. Mở Database Transaction.\n2. Kiểm tra tồn kho của tất cả sản phẩm trong đơn.\n3. Tính toán tổng tiền, trừ đi giá trị chiết khấu coupon.\n4. Lưu thông tin vào bảng orders và order_items.\n5. Trừ số lượng tồn kho tương ứng của từng biến thể sản phẩm.\n6. Xóa các sản phẩm đã mua khỏi giỏ hàng.\n7. Commit Transaction và chuyển hướng sang trang Hoàn tất đơn hàng."
    t_uc3.rows[5].cells[0].paragraphs[0].text = "Luồng ngoại lệ"
    t_uc3.rows[5].cells[1].paragraphs[0].text = "Nếu số lượng tồn kho của bất kỳ biến thể nào không đủ: Rollback toàn bộ giao dịch và báo lỗi chi tiết cho khách hàng."
    t_uc3.rows[6].cells[0].paragraphs[0].text = "Điều kiện sau khi thực hiện"
    t_uc3.rows[6].cells[1].paragraphs[0].text = "Đơn hàng mới xuất hiện trên hệ thống với trạng thái 'pending' và tồn kho đã được cập nhật chính xác."
    format_table(t_uc2, col_widths=[Cm(4.5), Cm(11.5)])

    add_para("", space_after=10)

    # UC 3
    add_heading_4("Bảng đặc tả Use Case UC-03: Phân công ca trực bán hàng")
    t_uc3 = doc.add_table(rows=6, cols=2)
    t_uc3.rows[0].cells[0].paragraphs[0].text = "Mã Use Case / Tên"
    t_uc3.rows[0].cells[1].paragraphs[0].text = "UC-03: Phân công lịch trực ca bán hàng (Sales Schedules)"
    t_uc3.rows[1].cells[0].paragraphs[0].text = "Tác nhân (Actor)"
    t_uc3.rows[1].cells[1].paragraphs[0].text = "Quản trị viên (Admin)"
    t_uc3.rows[2].cells[0].paragraphs[0].text = "Điều kiện tiên quyết"
    t_uc3.rows[2].cells[1].paragraphs[0].text = "Quản trị viên đã đăng nhập và hệ thống đã có tài khoản nhân viên vai trò 'sales'."
    t_uc3.rows[3].cells[0].paragraphs[0].text = "Luồng sự kiện chính"
    t_uc3.rows[3].cells[1].paragraphs[0].text = "1. Quản trị viên truy cập mục 'Lịch trực bán hàng' trên menu Admin.\n2. Chọn nhân viên bán hàng từ danh sách dropdown.\n3. Chọn ngày làm việc và chọn ca trực (Ca Sáng / Chiều / Tối).\n4. Nhập ghi chú giao việc (nếu có) và bấm 'Lưu phân công'."
    t_uc3.rows[4].cells[0].paragraphs[0].text = "Phản ứng của hệ thống"
    t_uc3.rows[4].cells[1].paragraphs[0].text = "1. Kiểm tra dữ liệu hợp lệ (user_id tồn tại, định dạng ngày chuẩn).\n2. Tạo bản ghi mới trong bảng sales_schedules với trạng thái 'assigned'.\n3. Hiển thị thông báo thành công và cập nhật bảng danh sách lịch trực."
    t_uc3.rows[5].cells[0].paragraphs[0].text = "Điều kiện sau khi thực hiện"
    t_uc3.rows[5].cells[1].paragraphs[0].text = "Nhân viên bán hàng tương ứng sẽ nhìn thấy ca trực này ngay khi đăng nhập vào hệ thống."
    format_table(t_uc3, col_widths=[Cm(4.5), Cm(11.5)])

    add_heading_2("2.4 Thiết kế Cơ sở dữ liệu (Chi tiết cấu trúc 47 bảng)")
    add_para("Cơ sở dữ liệu của dự án GiaDụngPro được xây dựng hoàn chỉnh gồm 47 bảng quan hệ logic, phân chia theo 10 phân hệ nghiệp vụ bài bản:")

    # Table 1: Users
    add_heading_3("Bảng 2.1: Cấu trúc bảng users (Người dùng & Phân quyền)")
    users_data = [
        ("Tên trường", "Kiểu dữ liệu", "Khóa / Ràng buộc", "Mặc định", "Mô tả nghiệp vụ"),
        ("id", "BIGINT UNSIGNED", "Primary Key, Auto Increment", "None", "Khóa chính định danh tài khoản"),
        ("name", "VARCHAR(255)", "NOT NULL", "None", "Họ và tên đầy đủ của người dùng"),
        ("avatar", "VARCHAR(255)", "NULL", "NULL", "Đường dẫn ảnh đại diện đại diện"),
        ("username", "VARCHAR(255)", "UNIQUE, NULL", "NULL", "Tên đăng nhập hệ thống"),
        ("email", "VARCHAR(255)", "UNIQUE, NOT NULL", "None", "Địa chỉ email liên hệ / đăng nhập"),
        ("phone", "VARCHAR(20)", "NULL", "NULL", "Số điện thoại liên lạc"),
        ("password", "VARCHAR(255)", "NOT NULL", "None", "Mật khẩu mã hóa Bcrypt"),
        ("address", "VARCHAR(255)", "NULL", "NULL", "Địa chỉ mặc định"),
        ("role", "VARCHAR(50)", "NOT NULL", "'user'", "Vai trò: admin, warehouse, sales, user"),
        ("status", "VARCHAR(50)", "NOT NULL", "'active'", "Trạng thái: active (hoạt động), locked (khóa)"),
        ("points", "INT", "NOT NULL", "0", "Điểm thưởng tích lũy của khách hàng"),
        ("created_at", "TIMESTAMP", "NULL", "NULL", "Thời điểm tạo tài khoản"),
        ("updated_at", "TIMESTAMP", "NULL", "NULL", "Thời điểm cập nhật gần nhất"),
    ]
    t_u = doc.add_table(rows=len(users_data), cols=5)
    for r_idx, row in enumerate(users_data):
        for c_idx, val in enumerate(row):
            t_u.rows[r_idx].cells[c_idx].paragraphs[0].text = val
    format_table(t_u, col_widths=[Cm(2.5), Cm(3.5), Cm(4.0), Cm(2.0), Cm(4.0)])

    add_para("", space_after=10)

    # Table 2: Products
    add_heading_3("Bảng 2.2: Cấu trúc bảng products (Thông tin sản phẩm chính)")
    products_data = [
        ("Tên trường", "Kiểu dữ liệu", "Khóa / Ràng buộc", "Mặc định", "Mô tả nghiệp vụ"),
        ("id", "BIGINT UNSIGNED", "Primary Key, Auto Increment", "None", "Khóa chính định danh sản phẩm"),
        ("category_id", "BIGINT UNSIGNED", "Foreign Key (categories.id)", "None", "Khóa ngoại liên kết danh mục"),
        ("brand_id", "BIGINT UNSIGNED", "Foreign Key (brands.id), NULL", "NULL", "Khóa ngoại liên kết thương hiệu"),
        ("name", "VARCHAR(255)", "NOT NULL", "None", "Tên thương mại của sản phẩm"),
        ("slug", "VARCHAR(255)", "UNIQUE, NOT NULL", "None", "Đường dẫn URL thân thiện SEO"),
        ("code", "VARCHAR(50)", "UNIQUE, NOT NULL", "None", "Mã sản phẩm nội bộ"),
        ("description", "TEXT", "NULL", "NULL", "Bài viết mô tả chi tiết sản phẩm"),
        ("price", "DECIMAL(12,2)", "NOT NULL", "None", "Giá bán thực tế hiển thị"),
        ("original_price", "DECIMAL(12,2)", "NULL", "NULL", "Giá gốc trước khi giảm (giá gạch)"),
        ("stock", "INT", "NOT NULL", "0", "Số lượng hàng tồn kho khả dụng"),
        ("unit", "VARCHAR(50)", "NOT NULL", "'Cái'", "Đơn vị tính: Cái, Bộ, Chiếc"),
        ("image", "VARCHAR(255)", "NULL", "NULL", "Ảnh đại diện chính của sản phẩm"),
        ("is_featured", "TINYINT(1)", "NOT NULL", "0", "Đánh dấu sản phẩm nổi bật trang chủ"),
        ("is_domestic", "TINYINT(1)", "NOT NULL", "1", "Xuất xứ: 1-Nội địa, 0-Nhập khẩu"),
        ("status", "VARCHAR(50)", "NOT NULL", "'active'", "Trạng thái kinh doanh: active/hidden"),
    ]
    t_p = doc.add_table(rows=len(products_data), cols=5)
    for r_idx, row in enumerate(products_data):
        for c_idx, val in enumerate(row):
            t_p.rows[r_idx].cells[c_idx].paragraphs[0].text = val
    format_table(t_p, col_widths=[Cm(2.5), Cm(3.5), Cm(4.0), Cm(2.0), Cm(4.0)])

    add_para("", space_after=10)

    # Table 3: Product Variants
    add_heading_3("Bảng 2.3: Cấu trúc bảng product_variants (Biến thể sản phẩm)")
    pv_data = [
        ("Tên trường", "Kiểu dữ liệu", "Khóa / Ràng buộc", "Mặc định", "Mô tả nghiệp vụ"),
        ("id", "BIGINT UNSIGNED", "Primary Key, Auto Increment", "None", "Khóa chính biến thể"),
        ("product_id", "BIGINT UNSIGNED", "Foreign Key (products.id)", "None", "Khóa ngoại sản phẩm cha"),
        ("color_id", "BIGINT UNSIGNED", "Foreign Key (colors.id), NULL", "NULL", "Khóa ngoại màu sắc"),
        ("size_id", "BIGINT UNSIGNED", "Foreign Key (sizes.id), NULL", "NULL", "Khóa ngoại kích thước/dung tích"),
        ("warranty_id", "BIGINT UNSIGNED", "Foreign Key (warranties.id), NULL", "NULL", "Khóa ngoại chính sách bảo hành"),
        ("sku", "VARCHAR(100)", "UNIQUE, NOT NULL", "None", "Mã lưu kho SKU biến thể"),
        ("price", "DECIMAL(12,2)", "NOT NULL", "None", "Giá bán riêng của biến thể"),
        ("stock", "INT", "NOT NULL", "0", "Số lượng tồn kho của biến thể"),
        ("image", "VARCHAR(255)", "NULL", "NULL", "Ảnh chụp thực tế của biến thể"),
    ]
    t_pv = doc.add_table(rows=len(pv_data), cols=5)
    for r_idx, row in enumerate(pv_data):
        for c_idx, val in enumerate(row):
            t_pv.rows[r_idx].cells[c_idx].paragraphs[0].text = val
    format_table(t_pv, col_widths=[Cm(2.5), Cm(3.5), Cm(4.0), Cm(2.0), Cm(4.0)])

    add_para("", space_after=10)

    # Table 4: Orders
    add_heading_3("Bảng 2.4: Cấu trúc bảng orders (Quản lý đơn hàng)")
    orders_data = [
        ("Tên trường", "Kiểu dữ liệu", "Khóa / Ràng buộc", "Mặc định", "Mô tả nghiệp vụ"),
        ("id", "BIGINT UNSIGNED", "Primary Key, Auto Increment", "None", "Khóa chính mã đơn hàng"),
        ("user_id", "BIGINT UNSIGNED", "Foreign Key (users.id), NULL", "NULL", "Khóa ngoại người đặt hàng"),
        ("order_number", "VARCHAR(50)", "UNIQUE, NOT NULL", "None", "Mã đơn hàng hiển thị (ORD-...)"),
        ("total_amount", "DECIMAL(12,2)", "NOT NULL", "None", "Tổng số tiền thanh toán của đơn"),
        ("status", "VARCHAR(50)", "NOT NULL", "'pending'", "Trạng thái: pending, processing, completed, cancelled"),
        ("payment_status", "VARCHAR(50)", "NOT NULL", "'unpaid'", "Trạng thái thanh toán: unpaid, paid"),
        ("customer_name", "VARCHAR(255)", "NOT NULL", "None", "Họ tên người nhận hàng"),
        ("customer_phone", "VARCHAR(20)", "NOT NULL", "None", "Số điện thoại nhận hàng"),
        ("shipping_address", "VARCHAR(255)", "NOT NULL", "None", "Địa chỉ chi tiết (Số nhà, đường)"),
        ("province_code", "VARCHAR(20)", "NULL", "NULL", "Mã Tỉnh / Thành phố"),
        ("district_code", "VARCHAR(20)", "NULL", "NULL", "Mã Quận / Huyện"),
        ("ward_code", "VARCHAR(20)", "NULL", "NULL", "Mã Phường / Xã"),
        ("coupon_code", "VARCHAR(50)", "NULL", "NULL", "Mã giảm giá đã sử dụng"),
        ("discount_amount", "DECIMAL(12,2)", "NOT NULL", "0.00", "Số tiền được giảm giá"),
    ]
    t_o = doc.add_table(rows=len(orders_data), cols=5)
    for r_idx, row in enumerate(orders_data):
        for c_idx, val in enumerate(row):
            t_o.rows[r_idx].cells[c_idx].paragraphs[0].text = val
    format_table(t_o, col_widths=[Cm(2.5), Cm(3.5), Cm(4.0), Cm(2.0), Cm(4.0)])

    add_para("", space_after=10)

    # Table 5: Sales Schedules
    add_heading_3("Bảng 2.5: Cấu trúc bảng sales_schedules (Phân công lịch trực bán hàng)")
    sched_data = [
        ("Tên trường", "Kiểu dữ liệu", "Khóa / Ràng buộc", "Mặc định", "Mô tả nghiệp vụ"),
        ("id", "BIGINT UNSIGNED", "Primary Key, Auto Increment", "None", "Khóa chính lịch trực"),
        ("user_id", "BIGINT UNSIGNED", "Foreign Key (users.id)", "None", "Khóa ngoại nhân viên bán hàng"),
        ("work_date", "DATE", "NOT NULL", "None", "Ngày thực hiện ca trực bán hàng"),
        ("shift_name", "VARCHAR(100)", "NOT NULL", "None", "Tên ca trực: Ca Sáng, Ca Chiều, Ca Tối"),
        ("notes", "TEXT", "NULL", "NULL", "Ghi chú phân công công việc cụ thể"),
        ("status", "VARCHAR(50)", "NOT NULL", "'assigned'", "Trạng thái: assigned, completed, cancelled"),
        ("created_by", "BIGINT UNSIGNED", "Foreign Key (users.id)", "None", "Người phân công (Quản trị viên)"),
        ("created_at", "TIMESTAMP", "NULL", "NULL", "Thời điểm tạo lịch trực"),
    ]
    t_s = doc.add_table(rows=len(sched_data), cols=5)
    for r_idx, row in enumerate(sched_data):
        for c_idx, val in enumerate(row):
            t_s.rows[r_idx].cells[c_idx].paragraphs[0].text = val
    format_table(t_s, col_widths=[Cm(2.5), Cm(3.5), Cm(4.0), Cm(2.0), Cm(4.0)])

    add_para("", space_after=10)
    add_para("Ngoài ra, hệ thống còn bao gồm các bảng chuyên trách khác đã được xây dựng và kết nối đồng bộ: carts & cart_items (quản lý giỏ hàng), categories (phân nhóm 49 danh mục), product_images (thư viện nhiều ảnh phụ), colors & sizes & warranties (thuộc tính biến thể), coupons & vouchers & user_vouchers (khuyến mãi), provinces & districts & wards (địa giới hành chính), games & game_rewards & game_attempts (vòng quay may mắn), daily_checkins (điểm danh), point_transactions & reward_items & reward_redemptions (kho quà tích điểm), product_reviews & review_votes (đánh giá), friendships (kết bạn), reports (khiếu nại vi phạm) và user_settings (cài đặt cá nhân).")

    doc.add_page_break()

    # =========================================================================
    # CHƯƠNG 3: CÀI ĐẶT THỰC NGHIỆM VÀ KẾT QUẢ ĐẠT ĐƯỢC
    # =========================================================================
    add_heading_1("Chương 3. CÀI ĐẶT THỰC NGHIỆM VÀ KẾT QUẢ ĐẠT ĐƯỢC")

    add_heading_2("3.1 Môi trường cài đặt và Quy trình triển khai")
    add_para("Hệ thống GiaDụngPro được cài đặt và vận hành thực nghiệm trên máy tính trạm với thông số cấu hình cụ thể được thể hiện trong Bảng 3.1:")

    env_data = [
        ("Thành phần", "Thông số thực tế", "Ghi chú cấu hình"),
        ("Hệ điều hành", "Microsoft Windows 11 Pro (x64)", "Môi trường phát triển cục bộ"),
        ("Nền tảng WAMP", "Laragon 6.0+", "Hỗ trợ Virtual Host tự động"),
        ("Ngôn ngữ thực thi", "PHP 8.3.30 (Cli/Cgi)", "Cấu hình memory_limit = 512M, intl, pdo_mysql"),
        ("Cơ sở dữ liệu", "MySQL 8.4.3 Community Server", "Port 3306, collation utf8mb4_unicode_ci"),
        ("Framework Backend", "Laravel 12.64.0", "Kiến trúc HasMiddleware, Eloquent ORM"),
        ("Công cụ đóng gói", "Vite 7.x & Tailwind CSS v4", "Biên dịch CSS/JS tối ưu hóa sản phẩm"),
        ("Tên miền ảo thử nghiệm", "http://giadung_1.test", "Laragon Virtual Host trỏ vào public/"),
    ]
    t_env = doc.add_table(rows=len(env_data), cols=3)
    for r_idx, row in enumerate(env_data):
        for c_idx, val in enumerate(row):
            t_env.rows[r_idx].cells[c_idx].paragraphs[0].text = val
    format_table(t_env, col_widths=[Cm(4.0), Cm(5.5), Cm(6.5)])
    add_para("Bảng 3.1: Thông số môi trường phần cứng và phần mềm triển khai thực nghiệm", italic=True, align=WD_ALIGN_PARAGRAPH.CENTER, space_before=4, space_after=14)

    add_heading_2("3.2 Giao diện và kết quả thực nghiệm Phân hệ Khách hàng")

    add_heading_3("3.2.1 Giao diện Đăng nhập & Đăng ký trượt chéo (Theatrical Sliding Auth)")
    add_para("Thay vì sử dụng hai trang đăng nhập và đăng ký rời rạc như các website truyền thống, GiaDụngPro đã cách tân trải nghiệm với thiết kế 'Ghost Button Diagonal Sliding Form':")
    add_para("• Khung container đồng nhất kích thước chuẩn max-w-4xl min-h-[580px] với sự phối hợp màu sắc tương phản cao cấp: Nền chính màu sáng Candy Blue (#B2D5E5) đi kèm chữ và nút bấm tối màu Onyx (#020202).")
    add_para("• Các ô nhập liệu phong cách gạch chân tối giản (Line-Style Inputs) sang trọng, loại bỏ khung viền bo tròn thô kệch.")
    add_para("• Hoạt họa quét chéo 6 giai đoạn (6-Stage Morphing Sweep Transition): Đỉnh đường chéo làm tâm điểm quét nghiêng phủ kín toàn màn hình, tráo đổi form ở phía sau và mở ngược ra để đưa form mới vào vị trí trung tâm mà không cần reload lại trang.")

    add_heading_3("3.2.2 Trang chủ với Hero Banner Parallax 3D và Hiệu ứng Focus/Repel")
    add_para("Trang chủ gây ấn tượng thị giác mạnh mẽ ngay từ cái nhìn đầu tiên với Hero Banner V2 điện ảnh:")
    add_para("• Chiều sâu không gian 3D Parallax: Ảnh nền căn bếp hiện đại kitchen-banner.png di chuyển êm ái theo tọa độ con trỏ chuột, tạo ảo giác không gian 3 chiều sống động.")
    add_para("• Cụm 3 vật phẩm bay bồng bềnh: Nồi chiên hơi nước Tefal FW5018, Máy xay đa năng Philips HR2221 và Nồi lẩu điện Sunhouse SHD4527 lơ lửng tự do với các chu kỳ chuyển động khác nhau.")
    add_para("• Hiệu ứng độc quyền Focus & Repel: Khi người dùng rê chuột vào một sản phẩm bất kỳ, sản phẩm đó sẽ tự động phóng to nổi bật (scale-118), trong khi hai sản phẩm còn lại tự động giãn nhẹ ra xa và làm dịu màu, giúp khách hàng tập trung quan sát chi tiết sản phẩm mình đang quan tâm.")

    add_heading_3("3.2.3 Lưới danh mục phân Tab và 3D Coverflow Carousel")
    add_para("• Lưới danh mục 49 ngành hàng được phân thành 4 tab trực quan: Thiết Bị Bếp, Lọc Nước & Khí, Làm Mát & Gia Dụng, Dọn Dẹp & Sức Khỏe. Khách hàng có thể chuyển tab tức thì với hiệu ứng Scale & Fade mượt mà.")
    add_para("• Khu vực thương hiệu đối tác được thiết kế dạng 3D Coverflow Carousel phỏng theo mẫu OriginKit, hỗ trợ vuốt chuột, bấm phím mũi tên hoặc tự động xoay thông minh (tự dừng khi rê chuột qua).")

    add_heading_3("3.2.4 Giỏ hàng và Đặt hàng liên hoàn địa giới 3 cấp")
    add_para("• Giỏ hàng hiển thị trực quan thông tin từng sản phẩm, màu sắc, kích cỡ, bảo hành kèm số lượng và thành tiền. Hệ thống tự động tính toán tổng tiền thời gian thực.")
    add_para("• Form đặt hàng tích hợp API địa lý Việt Nam 3 cấp: Khách hàng chọn Tỉnh/Thành phố -> danh sách Quận/Huyện tự động cập nhật -> chọn Quận/Huyện -> danh sách Phường/Xã tự động nạp. Điều này loại bỏ hoàn toàn tình trạng sai sót địa chỉ khi shipper giao nhận hàng.")

    add_heading_3("3.2.5 Hệ sinh thái Gamification & Điểm thưởng tích lũy")
    add_para("• Điểm danh hàng ngày (Daily Check-in): Giao diện lịch biểu trực quan, đánh dấu các ngày đã điểm danh và hiển thị số điểm nhận được cùng mốc thưởng chuỗi ngày.")
    add_para("• Vòng quay may mắn (Lucky Wheel): Vòng quay đồ họa Canvas sắc nét, âm thanh quay hồi hộp và tự động cập nhật số điểm hoặc voucher trúng thưởng vào ví tài khoản.")
    add_para("• Kho đổi quà: Danh sách các quà tặng hiện vật và voucher độc quyền được quy đổi minh bạch bằng điểm thưởng tích lũy.")

    add_heading_2("3.3 Giao diện và kết quả thực nghiệm Phân hệ Quản trị (Admin)")

    add_heading_3("3.3.1 Bảng điều khiển tổng quan (Dashboard)")
    add_para("Trang Dashboard (/admin) cung cấp góc nhìn kinh doanh toàn cảnh thời gian thực cho ban quản lý:")
    add_para("• Thẻ số liệu KPI: Tổng doanh thu thực tế (chỉ tính từ các đơn hàng hoàn thành), Tổng số đơn hàng, Tổng số sản phẩm và Tổng số khách hàng thành viên.")
    add_para("• Cảnh báo hàng tồn kho thấp: Tự động liệt kê danh sách các mặt hàng có số lượng tồn kho <= 5 chiếc với nút bấm cảnh báo đỏ, giúp nhân viên kho kịp thời lên kế hoạch nhập hàng.")
    add_para("• Danh sách đơn hàng mới nhất kèm trạng thái trực quan, cho phép xử lý đơn nhanh chóng.")

    add_heading_3("3.3.2 Quản lý Nhân sự và Phân quyền linh hoạt")
    add_para("Phân hệ Quản lý nhân viên (/admin/employees) cho phép Quản trị viên:")
    add_para("• Thêm mới tài khoản nhân sự với 2 chức danh nghiệp vụ: 'warehouse' (Nhân viên quản lý kho) và 'sales' (Nhân viên tư vấn bán hàng).")
    add_para("• Khóa / Mở khóa quyền truy cập của nhân viên khi có thay đổi nhân sự.")
    add_para("• Phân quyền thông minh: Nhân viên kho chỉ có quyền thêm/sửa/xóa sản phẩm và danh mục; Nhân viên bán hàng chỉ có quyền xem lịch trực và cập nhật ca làm việc cá nhân.")

    add_heading_3("3.3.3 Quản lý Lịch trực bán hàng (Sales Schedules Management)")
    add_para("Phân hệ Lịch trực bán hàng (/admin/sales-schedules) cung cấp giải pháp điều phối ca trực hoàn chỉnh:")
    add_para("• Quản trị viên phân ca trực theo ngày, chọn ca làm việc (Sáng, Chiều, Tối), phân công cho nhân viên và ghi chú công việc.")
    add_para("• Bộ lọc thông minh cho phép tra cứu lịch trực theo từng nhân viên, theo ngày cụ thể hoặc theo tháng.")
    add_para("• Nhân viên bán hàng xem lịch và bấm cập nhật hoàn thành ca trực, giúp quản trị viên nắm bắt tức thì tỷ lệ hoàn thành công việc của đội ngũ nhân sự.")

    add_heading_2("3.4 Kết quả Kiểm thử tự động (Automated Testing)")
    add_para("Nhằm đảm bảo chất lượng phần mềm đạt tiêu chuẩn cao nhất, toàn bộ hệ thống GiaDụngPro đã được bao phủ bởi bộ kiểm thử tự động (Automated Test Suite) nghiêm ngặt được viết bằng PHPUnit / Pest Framework:")

    test_results = [
        ("Bộ kiểm thử", "Tên ca kiểm thử (Test Case)", "Kết quả", "Thời gian chạy", "Ý nghĩa kiểm thử"),
        ("Tests\\Unit\\ExampleTest", "that true is true", "PASS", "0.61s", "Kiểm tra tính toàn vẹn của môi trường Test Runner."),
        ("Tests\\Feature\\AdvancedECommerceTest", "daily checkin streak calculates points", "PASS", "3.12s", "Kiểm thử tính điểm thưởng chuỗi ngày điểm danh liên tiếp."),
        ("Tests\\Feature\\AdvancedECommerceTest", "lucky wheel spin deducts points & rewards", "PASS", "0.14s", "Kiểm thử trừ điểm và trả về phần thưởng vòng quay may mắn."),
        ("Tests\\Feature\\AdvancedECommerceTest", "point redemption checks funds and stock", "PASS", "0.05s", "Kiểm thử đổi quà: kiểm tra số dư điểm và tồn kho quà."),
        ("Tests\\Feature\\AdvancedECommerceTest", "friendships workflow", "PASS", "0.09s", "Kiểm thử toàn bộ luồng gửi, chấp nhận, từ chối kết bạn."),
        ("Tests\\Feature\\AdvancedECommerceTest", "merge session cart to db on login", "PASS", "0.32s", "Kiểm thử hợp nhất giỏ hàng từ Session vào DB khi đăng nhập."),
        ("Tests\\Feature\\ECommerceTest", "register and login", "PASS", "0.09s", "Kiểm thử quy trình đăng ký và đăng nhập xác thực tài khoản."),
        ("Tests\\Feature\\ECommerceTest", "guest and customer cannot access admin", "PASS", "0.03s", "Kiểm thử phân quyền bảo mật: chặn khách thường vào /admin."),
        ("Tests\\Feature\\ECommerceTest", "admin can manage category", "PASS", "0.05s", "Kiểm thử quyền CRUD danh mục sản phẩm của quản trị viên."),
        ("Tests\\Feature\\ECommerceTest", "cannot add more than stock", "PASS", "0.04s", "Kiểm thử ràng buộc: không cho phép thêm giỏ vượt quá tồn kho."),
        ("Tests\\Feature\\ECommerceTest", "place order success decrements stock", "PASS", "0.05s", "Kiểm thử đặt hàng: trừ tồn kho và dọn sạch giỏ hàng."),
        ("Tests\\Feature\\ECommerceTest", "customer cannot view other users order", "PASS", "0.44s", "Kiểm thử bảo mật: ngăn khách xem đơn hàng của người khác."),
        ("Tests\\Feature\\ECommerceTest", "cancel order restores stock", "PASS", "0.05s", "Kiểm thử hủy đơn hàng: tự động hoàn trả tồn kho sản phẩm."),
        ("Tests\\Feature\\ECommerceTest", "historical price retained when price changes", "PASS", "0.02s", "Kiểm thử bảo lưu giá lịch sử trong đơn khi sản phẩm đổi giá."),
        ("Tests\\Feature\\ECommerceTest", "location cascading data", "PASS", "0.03s", "Kiểm thử API nạp dữ liệu địa giới Tỉnh -> Huyện -> Xã."),
        ("Tests\\Feature\\ECommerceTest", "apply coupon success and failures", "PASS", "0.03s", "Kiểm thử áp dụng mã giảm giá Coupon hợp lệ và hết hạn."),
        ("Tests\\Feature\\ECommerceTest", "checkout advanced methods", "PASS", "0.05s", "Kiểm thử quy trình thanh toán nâng cao."),
        ("Tests\\Feature\\FullE2EPurchasingFlowTest", "complete end to end purchasing flow", "PASS", "1.05s", "Kiểm thử toàn trình: Từ chọn hàng, thanh toán đến hoàn tất."),
    ]

    t_tr = doc.add_table(rows=len(test_results) + 1, cols=5)
    t_tr.rows[0].cells[0].paragraphs[0].text = "Tệp kiểm thử"
    t_tr.rows[0].cells[1].paragraphs[0].text = "Tên kịch bản kiểm thử"
    t_tr.rows[0].cells[2].paragraphs[0].text = "Trạng thái"
    t_tr.rows[0].cells[3].paragraphs[0].text = "Thời gian"
    t_tr.rows[0].cells[4].paragraphs[0].text = "Ý nghĩa kiểm thử"

    for r_idx, (suite, name, res, dur, desc) in enumerate(test_results):
        t_tr.rows[r_idx+1].cells[0].paragraphs[0].text = suite.split('\\')[-1]
        t_tr.rows[r_idx+1].cells[1].paragraphs[0].text = name
        t_tr.rows[r_idx+1].cells[2].paragraphs[0].text = res
        t_tr.rows[r_idx+1].cells[3].paragraphs[0].text = dur
        t_tr.rows[r_idx+1].cells[4].paragraphs[0].text = desc

    format_table(t_tr, col_widths=[Cm(3.5), Cm(4.5), Cm(1.5), Cm(1.5), Cm(5.0)])
    add_para("Bảng 3.2: Bảng tổng hợp kết quả chạy bộ kiểm thử tự động 18 kịch bản (105 assertions)", italic=True, align=WD_ALIGN_PARAGRAPH.CENTER, space_before=4, space_after=10)

    add_para("Tổng kết kiểm thử: 18 passed (105 assertions) - Tỷ lệ đạt 100%, không phát hiện bất kỳ lỗi hồi quy hay rò rỉ dữ liệu nào.", bold=True, color=RGBColor(22, 101, 52), align=WD_ALIGN_PARAGRAPH.CENTER)

    doc.add_page_break()

    # =========================================================================
    # CHƯƠNG 4: KẾT LUẬN VÀ HƯỚNG PHÁT TRIỂN
    # =========================================================================
    add_heading_1("Chương 4. KẾT LUẬN VÀ HƯỚNG PHÁT TRIỂN")

    add_heading_2("4.1 Đánh giá kết quả đạt được")
    add_para("Sau thời gian nghiêm túc nghiên cứu, phân tích và lập trình hoàn thiện, đề tài “Xây dựng website thương mại điện tử bán đồ gia dụng cao cấp - GiaDụngPro” đã đạt được những kết quả xuất sắc, bám sát các mục tiêu ban đầu đề ra:")
    add_para("1. Về mặt kiến trúc công nghệ: Ứng dụng thành công Laravel 12 kết hợp PHP 8.3 và Tailwind CSS v4, giải quyết triệt để lỗi thời của các cơ chế middleware cũ bằng cách chuẩn hóa theo interface HasMiddleware hiện đại. Cơ sở dữ liệu 47 bảng được chuẩn hóa cao độ, hỗ trợ đầy đủ các quan hệ phức tạp và toàn vẹn dữ liệu.")
    add_para("2. Về mặt trải nghiệm người dùng (UX/UI): Tạo nên bước đột phá lớn với giao diện Đăng ký/Đăng nhập trượt chéo rạp hát, Hero Banner Parallax 3D độc quyền, 3D Coverflow Carousel và lưới danh mục 49 ngành hàng phân tab mượt mà. Hệ thống hỗ trợ chế độ Dark Mode tự động và thân thiện với mọi kích cỡ màn hình.")
    add_para("3. Về mặt tính năng nghiệp vụ: Giải quyết trọn vẹn bài toán thương mại điện tử cho đồ gia dụng: quản lý biến thể đa thuộc tính, giỏ hàng đồng bộ, đặt hàng địa giới Việt Nam 3 cấp, kiểm soát tồn kho chặt chẽ bằng giao dịch DB Transaction và tự động hoàn kho khi hủy đơn.")
    add_para("4. Về mặt quản trị và nhân sự: Cung cấp đầy đủ công cụ quản lý doanh thu, quản lý nhân sự phân quyền theo vai trò kho (Warehouse) và tư vấn (Sales), đặc biệt là phân hệ Phân công lịch trực bán hàng (Sales Schedules) chuyên nghiệp.")
    add_para("5. Về mặt chất lượng phần mềm: Đạt tỷ lệ bao phủ kiểm thử tự động 100% với 18 kịch bản và 105 assertions vượt qua toàn bộ.")

    add_heading_2("4.2 Những hạn chế còn tồn tại")
    add_para("Mặc dù hệ thống đã hoạt động hoàn thiện và ổn định, song do giới hạn về nguồn lực và thời gian thực hiện đồ án, đề tài vẫn còn một số điểm có thể nâng cấp thêm:")
    add_para("• Phương thức thanh toán trực tuyến: Hiện tại hệ thống chủ yếu tập trung xử lý phương thức thanh toán tiền mặt khi nhận hàng (COD). Việc thanh toán qua thẻ tín dụng quốc tế hoặc các ví điện tử mới chỉ dừng ở mức mô phỏng quy trình thanh toán thành công.")
    add_para("• Hệ thống thông báo đẩy thời gian thực (Real-time Push Notifications): Thông báo hiện tại đang được lưu vào cơ sở dữ liệu và hiển thị khi người dùng tải lại hoặc truy cập trang thông báo, chưa ứng dụng WebSocket (như Laravel Reverb / Pusher) để đẩy thông báo nổi tức thì.")

    add_heading_2("4.3 Hướng nghiên cứu và phát triển tiếp theo")
    add_para("Trong giai đoạn tiếp theo, nhóm nghiên cứu dự kiến sẽ tiếp tục phát triển và mở rộng hệ thống GiaDụngPro theo các định hướng chiến lược sau:")
    add_para("1. Tích hợp cổng thanh toán trực tuyến chính thức: Kết nối API thanh toán với cổng thanh toán VNPay, MoMo, ZaloPay và thẻ thanh toán quốc tế Stripe / Paypal để mang lại trải nghiệm thanh toán đa dạng, an toàn tuyệt đối cho khách hàng.")
    add_para("2. Tích hợp Trợ lý ảo AI tư vấn đồ gia dụng (AI Household Advisor): Ứng dụng mô hình ngôn ngữ lớn (LLM) để xây dựng Chatbot thông minh ngay trên trang web, có khả năng tư vấn cho khách hàng nên mua nồi cơm điện nào, chảo chiên nào phù hợp với số lượng thành viên trong gia đình và diện tích căn bếp.")
    add_para("3. Tích hợp dịch vụ giao vận Logistics tự động: Kết nối API với các đơn vị vận chuyển hàng đầu tại Việt Nam (Giao Hàng Nhanh - GHN, Giao Hàng Tiết Kiệm - GHTK, Viettel Post) để tự động tính phí vận chuyển theo trọng lượng và in phiếu gửi hàng tự động.")
    add_para("4. Xây dựng ứng dụng di động (Mobile App): Sử dụng Flutter hoặc React Native để xây dựng ứng dụng di động GiaDụngPro trên cả hai hệ điều hành iOS và Android, đồng bộ dữ liệu trực tiếp với Backend Laravel hiện tại.")

    doc.add_page_break()

    # =========================================================================
    # TÀI LIỆU THAM KHẢO
    # =========================================================================
    add_heading_1("TÀI LIỆU THAM KHẢO")

    references = [
        "[1] Taylor Otwell and Laravel Core Team (2025). Laravel 12 Official Documentation. Truy cập tại: https://laravel.com/docs/12.x",
        "[2] Adam Wathan (2025). Tailwind CSS v4 Documentation & Core Concepts. Truy cập tại: https://tailwindcss.com/docs",
        "[3] The PHP Group (2024). PHP 8.3 Manual and Language Reference. Truy cập tại: https://www.php.net/manual/en/",
        "[4] Evan You and Vite Contributors (2025). Vite - Next Generation Frontend Tooling. Truy cập tại: https://vite.dev/",
        "[5] Oracle Corporation (2024). MySQL 8.4 Reference Manual. Truy cập tại: https://dev.mysql.com/doc/refman/8.4/en/",
        "[6] Khoa Công nghệ Thông tin - Trường Cao đẳng CNTT TP.HCM (2024). Mẫu hướng dẫn trình bày báo cáo đồ án môn học và khóa luận tốt nghiệp.",
        "[7] Martin Fowler (2002). Patterns of Enterprise Application Architecture. Addison-Wesley Professional.",
        "[8] Robert C. Martin (2008). Clean Code: A Handbook of Agile Software Craftsmanship. Prentice Hall.",
        "[9] Mozilla Developer Network (MDN) (2024). CSS 3D Transforms and Web APIs Reference. Truy cập tại: https://developer.mozilla.org/",
        "[10] W3C Web Accessibility Initiative (WAI) (2023). Web Content Accessibility Guidelines (WCAG) 2.2.",
    ]

    for ref in references:
        add_para(ref, font_size=12, space_before=2, space_after=6, line_spacing=1.3)

    # Save document
    output_filename = "BAO_CAO_DO_AN_GIADUNGPRO.docx"
    doc.save(output_filename)
    print(f"Successfully generated: {output_filename}")

    # Also copy or save to HOVATEN_MSSV_BAOCAO.docx for convenience
    doc.save("HOVATEN_MSSV_BAOCAO.docx")
    print("Successfully generated: HOVATEN_MSSV_BAOCAO.docx")

if __name__ == "__main__":
    create_report()
