/*//////////////////////////////////////////////////////////////////////////////
 //
 //    Copyright (c) GreenPrint Technologies LLC. 2019
 //
 //    File Name         : ToastView.swift
 //
 //    File Created      : 04:Mar:2020
 //
 //    Dev Name          : Sachin Kumar K.
 //
 //    Description       : Toast notifier view.
 //
 //////////////////////////////////////////////////////////////////////////// */

import UIKit

@IBDesignable
class ToastView: UILabel {
    public var animationDuration: Double = 2
    
    init(msg: String = "") {
        let frame = CGRect.zero
        super.init(frame: frame)
        self.text = msg
        commonInit()
    }
    
    required init?(coder: NSCoder) {
        super.init(coder: coder)
        commonInit()
    }
    
    private func commonInit() {
        // Setup label
        let screen = UIScreen.main.bounds
        let maxSize = CGSize(width: screen.width*0.8, height: 30)
        frame.size = sizeThatFits(maxSize)
        center = CGPoint(x: screen.midX, y: screen.height*0.9)
        layer.cornerRadius = bounds.height/2
        font = font.withSize(12)
        textAlignment = .center
        layer.masksToBounds = true
        backgroundColor = g_colorMode.invertColor()
        textColor = g_colorMode.defaultColor()
        alpha = 0.0
    }
    
    /// Call when toast show. (Automatically removed after animation)
    public func showMessage(with duration: Double? = nil) {
        var durAnimation: Double!
        if nil != duration {
            durAnimation = duration!
        }
        else {
            durAnimation = animationDuration
        }
        UIView.animate(withDuration: 0.5, animations: {
            // Show animation in fade
            self.alpha = 0.7
        }) {
            _ in
            UIView.animate(withDuration: 0.5, delay: durAnimation, animations: {
                // Hide and remove.
                self.alpha = 0.0
            }) {
                _ in
                self.removeFromSuperview()
            }
        }
    }
}
