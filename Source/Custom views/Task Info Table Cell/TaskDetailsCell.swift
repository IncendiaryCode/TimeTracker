/*//////////////////////////////////////////////////////////////////////////////
 //
 //    Copyright (c) GreenPrint Technologies LLC. 2019
 //
 //    File Name         : TaskDetailsCell.swift
 //
 //    File Created      : 09:Sept:2019
 //
 //    Dev Name          : Sachin Kumar K.
 //
 //    Description       : Tableview cell for task details.
 //
 //////////////////////////////////////////////////////////////////////////// */

import UIKit

class TaskDetailsCell: UITableViewCell {
    @IBOutlet weak var lblTotalDuration: UILabel!
    @IBOutlet weak var lblCategory: UILabel!
    @IBOutlet weak var lblStartTime: UILabel!
    @IBOutlet weak var lblTaskName: UILabel!
    @IBOutlet weak var lblProjectName: UILabel!
    @IBOutlet weak var imgVProjectIcon: UIImageView!
    @IBOutlet weak var imgTimer: UIImageView!
    
    var gradientLayer: CAGradientLayer!
    var shadowLayer: CAShapeLayer!
    var ntaskId: Int!
    var bTaskRunning = false
    var timer: Timer?
    var nTotalTime: Int!
    
    override func awakeFromNib() {
        super.awakeFromNib()
    }
    
    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)
    }
    
    override func layoutSubviews() {
        super.layoutSubviews()
        if shadowLayer == nil {
            // Draw drop shadow.
            shadowLayer = CAShapeLayer()
            if #available(iOS 11, *) {
                shadowLayer.path = UIBezierPath(roundedRect: bounds, cornerRadius: 20).cgPath
            }
            else {
                shadowLayer.path = UIBezierPath(roundedRect: bounds, cornerRadius: 0).cgPath
            }
            shadowLayer.fillColor = UIColor.white.cgColor
            shadowLayer.shadowPath = shadowLayer.path
            shadowLayer.shadowOffset = CGSize(width: 5, height: 5)
            shadowLayer.shadowOpacity = 0.15
            shadowLayer.shadowRadius = 5
            layer.insertSublayer(shadowLayer, at: 0)

            // Set cornering.
            contentView.layer.masksToBounds = true
            if #available(iOS 11, *) {
                layer.cornerRadius = 20
                contentView.layer.cornerRadius = 20
            }
            contentView.layer.borderWidth = 1
            contentView.layer.borderColor = UIColor.lightGray.withAlphaComponent(0.2).cgColor
            backgroundColor = .clear
            selectionStyle = .none
            
            // Setup gradient.
            gradientLayer = CAGradientLayer()
            
            gradientLayer.locations = [0.5 , 1.0]
            gradientLayer.startPoint = CGPoint(x: 0.0, y: 0.0)
            gradientLayer.endPoint = CGPoint(x: 1.0, y: 1.0)
            gradientLayer.cornerRadius = 20
            gradientLayer.frame = CGRect(x: 0, y: 0.0, width: frame.size.width
                , height: frame.size.height)
            contentView.layer.insertSublayer(gradientLayer, at: 0)
        }

        
        // Check running task.
        if lblTotalDuration.text == "Running" || lblTotalDuration.text == "Synching" ||
                nil != timer {
            gradientLayer.colors = [UIColor.lightGray
                .withAlphaComponent(0.02).cgColor, UIColor.lightGray
                    .withAlphaComponent(0.3).cgColor]
            shadowLayer.shadowColor = g_colorMode.invertColor().withAlphaComponent(1).cgColor
            contentView.layer.borderColor = UIColor.lightGray.withAlphaComponent(0.4).cgColor
            imgTimer.tintColor = g_colorMode.midColor()
        }
        else {
            gradientLayer.colors = []
            shadowLayer.shadowColor = g_colorMode.invertColor().withAlphaComponent(0.5).cgColor
            contentView.layer.borderColor = UIColor.lightGray.withAlphaComponent(0.2).cgColor
            imgTimer.tintColor = .clear
        }
        selectionStyle = .none
        contentView.backgroundColor = g_colorMode.defaultColor()
    }
    
    @objc func timerAction() {
        //Update counter label.
        self.nTotalTime += 1
        lblTotalDuration.text = "\(getSecondsToHoursMinutesSeconds(seconds: self.nTotalTime))"
    }
    
    override var frame: CGRect {
        get {
            return super.frame
        }
        set (newFrame) {
            guard #available(iOS 11, *) else {
                super.frame = newFrame
                return
            }
            var frame =  newFrame
            frame.origin.y += 10
            frame.origin.x += 12
            frame.size.width -= 24
            frame.size.height -= 15
            super.frame = frame
        }
    }
}
