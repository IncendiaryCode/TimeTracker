//
//  ProgressCircle.swift
//  Attendance Manager
//
//  Created by Sachin on 9/18/19.
//  Copyright © 2019 Sachin. All rights reserved.
//

import Foundation
import UIKit

class ProgressCircle: UIImageView {
    let progressShape = CAShapeLayer()
    let backgroundShape = CAShapeLayer()
    let percent = 100.0
    
    override func awakeFromNib() {
        super.awakeFromNib()
        
        self.progressShape.frame = CGRect(x: 53, y: 31, width: 150, height: 150)
        self.backgroundShape.frame = CGRect(x: 53, y: 31, width: 150, height: 150)
        self.layer.addSublayer(backgroundShape)
        self.layer.addSublayer(progressShape)
        updateIndicator(with: 0)
    }
    func updateIndicator(with percent: Double, isAnimated: Bool = false) {
        let animation = CABasicAnimation(keyPath: "strokeEnd")
        animation.fromValue = progressShape.strokeEnd
        animation.toValue = percent / 100.0
        animation.duration = 2.5
        animation.timingFunction = CAMediaTimingFunction(name: CAMediaTimingFunctionName.easeInEaseOut);
        let strokeWidth: CGFloat = 14.0
        let frame = CGRect(x: 0, y: 0, width: 73, height: 73)
        //backgroundShape.frame = frame
       // backgroundShape.position = self.center
        backgroundShape.path = UIBezierPath(ovalIn: frame).cgPath
        backgroundShape.strokeColor = cgCForGradientStart
        backgroundShape.lineWidth = strokeWidth
        backgroundShape.fillColor = UIColor.clear.cgColor
        //progressShape.frame = frame
        progressShape.path = backgroundShape.path
        //progressShape.position = backgroundShape.position
        progressShape.strokeColor = UIColor.white.cgColor
        progressShape.lineWidth = backgroundShape.lineWidth
        progressShape.fillColor = UIColor.clear.cgColor
        progressShape.strokeEnd = CGFloat(percent/100.0)
        if isAnimated {
            progressShape.add(animation, forKey: nil)
        }
    }
    
    override func layoutSubviews() {
        super.layoutSubviews()
        updateIndicator(with: percent, isAnimated: true)
    }
}
