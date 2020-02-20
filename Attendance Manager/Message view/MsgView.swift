/*//////////////////////////////////////////////////////////////////////////////
 //
 //    Copyright (c) GreenPrint Technologies LLC. 2019
 //
 //    File Name         : MsgView.swift
 //
 //    File Created      : 18:Jan:2020
 //
 //    Dev Name          : Sachin Kumar K.
 //
 //    Description       : Message view.
 //
 //////////////////////////////////////////////////////////////////////////// */

import UIKit

@IBDesignable
class MsgView: UIView {
    /// Label text.
    private var lblMsg: UILabel!
    private var selfFrame: CGRect!
    
    override var frame: CGRect {
        didSet {
            selfFrame = frame
        }
    }
    
    /// To change text in msg view.
    public func setMsg(msg: String) {
        self.transform = CGAffineTransform.identity
        self.frame = selfFrame
        lblMsg.text = msg
        lblMsg.textColor = g_colorMode.defaultColor()
        
        // Dynamic based on text size.
        let width = lblMsg.intrinsicContentSize.width+10
        lblMsg.frame.size = CGSize(width: width, height: lblMsg.bounds.height)
        lblMsg.center = CGPoint(x: bounds.midX, y: bounds.midY-4)
        self.frame.size = CGSize(width: width, height: self.bounds.height)
        setNeedsDisplay()
        self.transform = CGAffineTransform.identity.scaledBy(x: 0.0001, y: 0.0001)
    }
    
    init(frame: CGRect, msg: String = "") {
        super.init(frame: frame)
        // Setup label constraints.
        commonInit()
        lblMsg.text = msg
        selfFrame = frame
        // Dynamic based on text size.
        let width = lblMsg.intrinsicContentSize.width+10
        lblMsg.frame.size = CGSize(width: width, height: lblMsg.bounds.height)
        self.frame.size = CGSize(width: width, height: self.bounds.height)
        setNeedsDisplay()
        self.transform = CGAffineTransform.identity.scaledBy(x: 0.0001, y: 0.0001)
    }
    
    private func commonInit() {
        let cgRect = CGRect(origin: .zero, size: CGSize(width: frame.width, height: 30))
        lblMsg = UILabel(frame: cgRect)
        lblMsg.center = CGPoint(x: bounds.midX, y: bounds.midY-4)
        
        addSubview(lblMsg)
        lblMsg.textColor = .white
        lblMsg.textAlignment = .center
        lblMsg.font = lblMsg.font.withSize(12)
    }
    
    required init?(coder: NSCoder) {
        super.init(coder: coder)
        commonInit()
    }
    
    /// To show view with animation.
    public func showView(with duration: Double = 0.5) {
        if self.transform != CGAffineTransform.identity {
            self.layer.removeAllAnimations()
            self.transform = CGAffineTransform.identity.scaledBy(x: 0.0001, y: 0.0001)
            self.transform.ty = 25
            UIView.animate(withDuration: duration, animations: {
                self.transform = CGAffineTransform.identity
            })
        }
    }
    
    /// To hide view with animation.
    public func hideView(with duration: Double = 0.5) {
        if self.transform == CGAffineTransform.identity {
            self.layer.removeAllAnimations()
            var transform = CGAffineTransform.identity.scaledBy(x: 0.0001, y: 0.0001)
            transform.ty = 25
            UIView.animate(withDuration: duration, animations: {
                self.transform = transform
            })
        }
    }
    
    // Drawing background view.
    override func draw(_ rect: CGRect) {
        super.draw(rect)
        let arrowXOffset: CGFloat = rect.midX - 10
        let cornerRadius: CGFloat = 8
        let arrowHeight: CGFloat = 10
        let mainRect = CGRect(origin: rect.origin, size: CGSize(width: rect.width, height: rect.height - arrowHeight))
        
        let leftTopPoint = mainRect.origin
        let rightTopPoint = CGPoint(x: mainRect.maxX, y: mainRect.minY)
        let rightBottomPoint = CGPoint(x: mainRect.maxX, y: mainRect.maxY)
        let leftBottomPoint = CGPoint(x: mainRect.minX, y: mainRect.maxY)
        
        let leftArrowPoint = CGPoint(x: leftBottomPoint.x + arrowXOffset, y: leftBottomPoint.y)
        let centerArrowPoint = CGPoint(x: leftArrowPoint.x + arrowHeight, y: leftArrowPoint.y + arrowHeight)
        let rightArrowPoint = CGPoint(x: leftArrowPoint.x + 2 * arrowHeight, y: leftArrowPoint.y)
        
        let path = UIBezierPath()
        path.addArc(withCenter: CGPoint(x: rightTopPoint.x - cornerRadius, y: rightTopPoint.y + cornerRadius), radius: cornerRadius,
                    startAngle: CGFloat(3 * Double.pi / 2), endAngle: CGFloat(2 * Double.pi), clockwise: true)
        path.addArc(withCenter: CGPoint(x: rightBottomPoint.x - cornerRadius, y: rightBottomPoint.y - cornerRadius), radius: cornerRadius,
                    startAngle: 0, endAngle: CGFloat(Double.pi / 2), clockwise: true)
        
        path.addLine(to: rightArrowPoint)
        path.addLine(to: centerArrowPoint)
        path.addLine(to: leftArrowPoint)
        
        path.addArc(withCenter: CGPoint(x: leftBottomPoint.x + cornerRadius, y: leftBottomPoint.y - cornerRadius), radius: cornerRadius,
                    startAngle: CGFloat(Double.pi / 2), endAngle: CGFloat(Double.pi), clockwise: true)
        path.addArc(withCenter: CGPoint(x: leftTopPoint.x + cornerRadius, y: leftTopPoint.y + cornerRadius), radius: cornerRadius,
                    startAngle: CGFloat(Double.pi), endAngle: CGFloat(3 * Double.pi / 2), clockwise: true)
        
        
        path.addLine(to: rightTopPoint)
        path.close()
        
        let shapeLayer = CAShapeLayer()
        shapeLayer.path = path.cgPath
        shapeLayer.fillColor = g_colorMode.invertColor().cgColor
        shapeLayer.shadowColor = g_colorMode.invertColor().cgColor
        shapeLayer.shadowOffset = CGSize.zero
        shapeLayer.shadowRadius = 5.0
        shapeLayer.shadowOpacity = 0.7
        shapeLayer.backgroundColor = UIColor.clear.cgColor
        self.layer.insertSublayer(shapeLayer, at: 0)
    }
}
