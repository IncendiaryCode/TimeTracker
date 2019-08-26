//
//  RadioWaveAnimataion.swift
//  Attendance Manager
//
//  Created by Sachin on 9/18/19.
//  Copyright © 2019 Sachin. All rights reserved.
//

import Foundation
import UIKit

class RadioWaveAnimationView: UIImageView {
    
    var animatableLayer : CAShapeLayer?
    
    override func awakeFromNib() {
        super.awakeFromNib()
        self.layer.cornerRadius = self.bounds.height/2
        
        self.animatableLayer = CAShapeLayer()
        self.animatableLayer?.fillColor = cgCForGradientStart
        self.animatableLayer?.path = UIBezierPath(roundedRect: self.bounds, cornerRadius: self.layer.cornerRadius).cgPath
        self.animatableLayer?.frame = self.bounds
        self.animatableLayer?.cornerRadius = self.bounds.height/2
//        self.animatableLayer?.masksToBounds = true
        self.layer.addSublayer(self.animatableLayer!)
        self.startAnimation()
    }
    
    
    func startAnimation()
    {
        let layerAnimation = CABasicAnimation(keyPath: "transform.scale")
        layerAnimation.fromValue = 1
        layerAnimation.toValue = 3
        layerAnimation.isAdditive = false
        
        let layerAnimation2 = CABasicAnimation(keyPath: "opacity")
        layerAnimation2.fromValue = 1
        layerAnimation2.toValue = 0
        layerAnimation2.isAdditive = false
        
//        let layerAnimation3 = CABasicAnimation(keyPath: "backgroundColor")
//        layerAnimation3.fromValue = cgCForGradientStart
//        layerAnimation3.toValue = cgCForGradientEnd
//        layerAnimation3.isAdditive = false
        
        let groupAnimation = CAAnimationGroup()
        groupAnimation.animations = [layerAnimation,layerAnimation2]
        groupAnimation.duration = CFTimeInterval(1)
        groupAnimation.fillMode = CAMediaTimingFillMode.both
        groupAnimation.isRemovedOnCompletion = true
        groupAnimation.repeatCount = .infinity
        
        self.animatableLayer?.add(groupAnimation, forKey: "growingAnimation")
    }
    /*
     // Only override draw() if you perform custom drawing.
     // An empty implementation adversely affects performance during animation.
     override func draw(_ rect: CGRect) {
     // Drawing code
     }
     */
    
}
