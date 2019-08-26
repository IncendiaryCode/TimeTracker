//
//  ActivityCellController.swift
//  Attendance Manager
//
//  Created by Sachin on 10/1/19.
//  Copyright © 2019 Sachin. All rights reserved.
//

import UIKit
import Charts

class ActivityCellController: UITableViewCell {
    @IBOutlet weak var viewCalender: UIView!
    @IBOutlet weak var lblDayWeekMonth: UILabel!
    @IBOutlet weak var lblSubDayWeek: UILabel!
    @IBOutlet weak var lblWorkDays: UILabel!
    @IBOutlet weak var lblNWorks: UILabel!
    @IBOutlet weak var lblWorkHours: UILabel!
    @IBOutlet weak var lblNWorkTime: UILabel!
    @IBOutlet weak var lblTotTask: UILabel!
    @IBOutlet weak var lblNTaskcount: UILabel!
    @IBOutlet weak var horizontalBarView: HorizontalBarChartView!
    
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
        viewCalender.addGradient(startColor: cgCForGradientStart, endColor: cgCForGradientEnd, cgFRadius: 5)
    }

    override func layoutSubviews() {
           super.layoutSubviews()
           self.layer.cornerRadius = 15
           self.layer.shadowOffset = CGSize(width: 0, height: 3)
           self.layer.shadowRadius = 3
           self.layer.shadowOpacity = 0.6
           self.layer.shadowColor = UIColor.gray.cgColor
           self.layer.shadowPath = UIBezierPath(roundedRect: self.bounds, byRoundingCorners:
               .allCorners, cornerRadii: CGSize(width: 8, height: 8)).cgPath
           self.layer.shouldRasterize = true
           self.layer.rasterizationScale = UIScreen.main.scale
       }
       
       override var frame: CGRect {
           get {
               return super.frame
           }
           set (newFrame) {
               var frame =  newFrame
               frame.origin.x += 12
               frame.origin.y += 12
               frame.size.width -= 2 * 12
               frame.size.height -= 2 * 6
               super.frame = frame
           }
       }
    
}
