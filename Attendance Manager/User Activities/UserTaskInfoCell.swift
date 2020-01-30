/*//////////////////////////////////////////////////////////////////////////////
 //
 //    Copyright (c) GreenPrint Technologies LLC. 2019
 //
 //    File Name         : UserTaskInfoCell.swift
 //
 //    File Created      : 09:Sept:2019
 //
 //    Dev Name          : Sachin Kumar K.
 //
 //    Description       : Tableview cell for task details.
 //
 //////////////////////////////////////////////////////////////////////////// */

import UIKit

class UserTaskInfoCell: UITableViewCell {
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
            shadowLayer.path = UIBezierPath(roundedRect: bounds, cornerRadius: 20).cgPath
            shadowLayer.fillColor = UIColor.white.cgColor
            shadowLayer.shadowPath = shadowLayer.path
            shadowLayer.shadowOffset = CGSize(width: 5, height: 5)
            shadowLayer.shadowOpacity = 0.15
            shadowLayer.shadowRadius = 5
            layer.insertSublayer(shadowLayer, at: 0)

            // Set cornering.
            contentView.layer.masksToBounds = true
            layer.cornerRadius = 20
            contentView.layer.cornerRadius = 20
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
                lblTotalDuration.text == "Stoping" {
            gradientLayer.colors = [UIColor.lightGray
                .withAlphaComponent(0.02).cgColor, UIColor.lightGray
                    .withAlphaComponent(0.3).cgColor]
            shadowLayer.shadowColor = g_colorMode.invertColor().withAlphaComponent(1).cgColor
            contentView.layer.borderColor = UIColor.lightGray.withAlphaComponent(0.4).cgColor
        }
        else {
            gradientLayer.colors = []
            shadowLayer.shadowColor = g_colorMode.invertColor().withAlphaComponent(0.5).cgColor
            contentView.layer.borderColor = UIColor.lightGray.withAlphaComponent(0.2).cgColor
        }
        selectionStyle = .none
        contentView.backgroundColor = g_colorMode.defaultColor()
    }
    
    override var frame: CGRect {
        get {
            return super.frame
        }
        set (newFrame) {
            var frame =  newFrame
            frame.origin.y += 10
            frame.origin.x += 12
            frame.size.width -= 24
            frame.size.height -= 15
            super.frame = frame
        }
    }
}
