/*//////////////////////////////////////////////////////////////////////////////
//
//    Copyright (c) GreenPrint Technologies LLC. 2019
//
//    File Name         : ProgressiveCircle.swift
//
//    File Created      : 18:Sept:2019
//
//    Dev Name          : Sachin Kumar K.
//
//    Description       : Progressive circle animation.
//
//////////////////////////////////////////////////////////////////////////// */

import Foundation
import UIKit

class ProgressCircle: UIImageView {
    let caProgressShape = CAShapeLayer()
    let caBackgroundShape = CAShapeLayer()
    let dPercent = 100.0
    
    override func awakeFromNib() {
        super.awakeFromNib()
        
        self.caProgressShape.frame = CGRect(x: 55, y: 31, width: 150, height: 150)
        self.caBackgroundShape.frame = CGRect(x: 55, y: 31, width: 150, height: 150)
        self.layer.addSublayer(caBackgroundShape)
        self.layer.addSublayer(caProgressShape)
        updateIndicator(with: 0)
    }
    
    func updateIndicator(with dPercent: Double, isAnimated: Bool = false) {
        let animation = CABasicAnimation(keyPath: "strokeEnd")
        
        //Add animation constraints.
        animation.fromValue = caProgressShape.strokeEnd
        animation.toValue = dPercent / 100.0
        animation.duration = 2
        animation.timingFunction = CAMediaTimingFunction(name: CAMediaTimingFunctionName
            .easeInEaseOut)
        
        // Specify width and frame for.
        let cgFStrokeWidth: CGFloat = 13
        let cgRFrame = CGRect(x: 0, y: 0, width: 70, height: 73)
        
        // Add CAShaper constarints.
        caBackgroundShape.path = UIBezierPath(ovalIn: cgRFrame).cgPath
        caBackgroundShape.strokeColor = g_colorMode.midColor().cgColor
        caBackgroundShape.lineWidth = cgFStrokeWidth
        caBackgroundShape.fillColor = UIColor.clear.cgColor
        caProgressShape.path = caBackgroundShape.path
        caProgressShape.strokeColor = UIColor.white.cgColor
        caProgressShape.lineWidth = caBackgroundShape.lineWidth
        caProgressShape.fillColor = UIColor.clear.cgColor
        caProgressShape.strokeEnd = CGFloat(dPercent/100.0)
        if isAnimated {
            caProgressShape.add(animation, forKey: nil)
        }
    }
    
    override func layoutSubviews() {
        super.layoutSubviews()
        updateIndicator(with: dPercent, isAnimated: true)
    }
}
