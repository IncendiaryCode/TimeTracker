//
//  userBreakInfoCell.swift
//  Attendance Manager
//
//  Created by Sachin on 9/6/19.
//  Copyright © 2019 Sachin. All rights reserved.
//

import UIKit

class UserTaskInfoCell: UITableViewCell {
    @IBOutlet weak var lblTotalDuration: UILabel!
    @IBOutlet weak var lblCategory: UILabel!
    @IBOutlet weak var lblStartTime: UILabel!
    @IBOutlet weak var lblTaskDescription: UILabel!
    @IBOutlet weak var lblProjectName: UILabel!
    @IBOutlet weak var imgVProjectIcon: UIImageView!
    
    var nTaskId: Int!
    var bTaskRunning = false
    
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }
    
    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)
//        let cgRFrame = UIEdgeInsets(top: 8, left: 8, bottom: 8, right: 8)
//        backgroundView?.frame = backgroundView?.frame.inset(by: UIEdgeInsets(top: 2, left: 0, bottom: 0, right: 0)) ?? CGRect.zero
        // Configure the view for the selected state
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
