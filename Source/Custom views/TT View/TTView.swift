/*//////////////////////////////////////////////////////////////////////////////
 //
 //    Copyright (c) GreenPrint Technologies LLC. 2019
 //
 //    File Name         : TTView.swift
 //
 //    File Created      : 26:Feb:2020
 //
 //    Dev Name          : Sachin Kumar K.
 //
 //    Description       : View contains background time tracker logo.
 //
 //////////////////////////////////////////////////////////////////////////// */

import UIKit

@IBDesignable
class TTView: UIView {
    
    /// Fill color to the drawn path.
    @IBInspectable var fillColor: UIColor = UIColor(hexString: "#b56cff")!
    /// Shadow Color.
    @IBInspectable var shadowColor: UIColor = UIColor.black.withAlphaComponent(0.50)
    @IBInspectable var shadowOffset: CGSize = CGSize(width: 4.0, height: 4.0)
    @IBInspectable var shadowBlur: CGFloat = 6.0
    /// Border size.
    @IBInspectable var borderWidth: CGFloat = 6.0
    /// length is choosen based on minimum size of width or height.
    private var minLength: CGFloat {
        return min(bounds.width, bounds.height)
    }
    /// Border width.
    private var circleBorderWidth: CGFloat {
        return minLength*borderWidth/100
    }
    /// View center.
    private var viewCenter: CGPoint {
        return CGPoint(x: bounds.midX+UIScreen.main.bounds.width*0.05, y: bounds.midY)
    }
    
    override func draw(_ rect: CGRect) {
        guard let context = UIGraphicsGetCurrentContext() else {
            return
        }
        
        // Outer main circle.
        var path = UIBezierPath(arcCenter: viewCenter, radius: minLength/5, startAngle: 0
            , endAngle: CGFloat(2*Double.pi), clockwise: true)
        path.lineWidth = circleBorderWidth
        fillColor.setStroke()
        context.setShadow(offset: shadowOffset, blur: shadowBlur, color: shadowColor.cgColor)
        path.stroke()
        path.removeAllPoints()
        
        // Center path.
        path.addArc(withCenter: viewCenter, radius: 3*circleBorderWidth/4, startAngle
            : CGFloat(7*Double.pi)/4, endAngle: CGFloat(Double.pi)/4, clockwise: false)
        let point = CGPoint(x: viewCenter.x+circleBorderWidth*2, y: viewCenter.y)
        path.addLine(to: point)
        path.close()
        fillColor.setFill()
        context.setShadow(offset: shadowOffset, blur: shadowBlur, color: shadowColor.cgColor)
        path.fill()
        path.removeAllPoints()
        
        // 3 left arrows.
        var lineBottom = CGPoint(x: viewCenter.x-minLength/5.0-circleBorderWidth, y:
            viewCenter.y+circleBorderWidth/2)
        var lineTop = CGPoint(x: viewCenter.x-minLength/5.0-circleBorderWidth, y:
            viewCenter.y-circleBorderWidth/2)
        var outerArcRadius = lineBottom.x-viewCenter.x
        var angle = asin(circleBorderWidth/outerArcRadius)
        
        // Mid line
        var cgPath = CGMutablePath()
        cgPath.addRelativeArc(center: viewCenter, radius: viewCenter.x-lineTop.x, startAngle: (CGFloat(Double.pi)-angle/2), delta: angle)
        
        var minLinePoint = CGPoint(x: lineBottom.x-minLength/9, y: lineBottom.y)
        var maxLinePoint = CGPoint(x: lineTop.x-minLength/7, y: lineTop.y)
        cgPath.addLine(to: minLinePoint)
        cgPath.addLine(to: maxLinePoint)
        cgPath.closeSubpath()
        
        context.addPath(cgPath)
        context.setFillColor(fillColor.cgColor)
        context.setShadow(offset: shadowOffset, blur: shadowBlur, color: shadowColor.cgColor)
        context.fillPath()
        
        // Top line
        cgPath = CGMutablePath()
        lineBottom = CGPoint(x: viewCenter.x-minLength/5.0-circleBorderWidth, y: viewCenter.y-circleBorderWidth*2+circleBorderWidth)
        lineTop = CGPoint(x: viewCenter.x-minLength/5.0-circleBorderWidth, y:
            viewCenter.y-circleBorderWidth*2)
        outerArcRadius = lineBottom.x-viewCenter.x
        angle = asin(circleBorderWidth/outerArcRadius)
        
        cgPath = CGMutablePath()
        cgPath.addRelativeArc(center: viewCenter, radius: viewCenter.x-lineTop.x, startAngle:
            (CGFloat(Double.pi)-angle*2), delta: angle)
        
        minLinePoint = CGPoint(x: lineBottom.x-minLength/8, y: lineBottom.y)
        maxLinePoint = CGPoint(x: lineTop.x-minLength/6, y: lineTop.y)
        cgPath.addLine(to: minLinePoint)
        cgPath.addLine(to: maxLinePoint)
        cgPath.closeSubpath()
        context.addPath(cgPath)
        context.setFillColor(fillColor.cgColor)
        context.setShadow(offset: shadowOffset, blur: shadowBlur, color: shadowColor.cgColor)
        context.fillPath()
        
        // Bottom line
        cgPath = CGMutablePath()
        lineBottom = CGPoint(x: viewCenter.x-minLength/5.0-circleBorderWidth, y:
            viewCenter.y+circleBorderWidth*2)
        lineTop = CGPoint(x: viewCenter.x-minLength/5.0-circleBorderWidth, y:
            viewCenter.y+circleBorderWidth*2-circleBorderWidth)
        
        outerArcRadius = lineBottom.x-viewCenter.x
        angle = asin(circleBorderWidth/outerArcRadius)
        
        cgPath = CGMutablePath()
        cgPath.addRelativeArc(center: viewCenter, radius: viewCenter.x-lineTop.x
            , startAngle: (CGFloat(Double.pi)+angle), delta: angle)
        
        minLinePoint = CGPoint(x: lineBottom.x-minLength/8, y: lineBottom.y)
        maxLinePoint = CGPoint(x: lineTop.x-minLength/6, y: lineTop.y)
        cgPath.addLine(to: minLinePoint)
        cgPath.addLine(to: maxLinePoint)
        cgPath.closeSubpath()
        
        context.addPath(cgPath)
        context.setFillColor(fillColor.cgColor)
        context.setShadow(offset: shadowOffset, blur: shadowBlur, color: shadowColor.cgColor)
        context.fillPath()
        
        // Right single line
        cgPath = CGMutablePath()
        lineBottom = CGPoint(x: viewCenter.x-minLength/5.0-circleBorderWidth
            , y: viewCenter.y+circleBorderWidth*2)
        lineTop = CGPoint(x: viewCenter.x-minLength/5.0-circleBorderWidth
            , y: viewCenter.y+circleBorderWidth*2-circleBorderWidth)
        
        outerArcRadius = lineBottom.x-viewCenter.x
        angle = asin(circleBorderWidth/outerArcRadius)
        
        cgPath = CGMutablePath()
        cgPath.closeSubpath()
        cgPath.addRelativeArc(center: viewCenter, radius: viewCenter.x-lineTop.x, startAngle: (CGFloat(7*Double.pi/4)+angle), delta: -angle)
        
        cgPath.addRelativeArc(center: viewCenter, radius: (viewCenter.x-lineTop
            .x)+circleBorderWidth, startAngle: (CGFloat(7*Double.pi/4)), delta: angle)
        
        cgPath.addRelativeArc(center: viewCenter, radius: viewCenter.x-lineTop.x
            , startAngle: (CGFloat(7*Double.pi/4)+angle), delta: -angle)
        cgPath.closeSubpath()
        context.addPath(cgPath)
        context.setFillColor(fillColor.cgColor)
        
        context.setShadow(offset: shadowOffset, blur: shadowBlur, color: shadowColor.cgColor)
        context.fillPath()
        
        // Top symbol.
        cgPath = CGMutablePath()
        lineBottom = CGPoint(x: viewCenter.x-minLength/5.0-circleBorderWidth
            , y: viewCenter.y+circleBorderWidth*2)
        lineTop = CGPoint(x: viewCenter.x-minLength/5.0-circleBorderWidth
            , y: viewCenter.y+circleBorderWidth*2-circleBorderWidth)
        
        outerArcRadius = lineBottom.x-viewCenter.x
        angle = asin(circleBorderWidth/outerArcRadius)
        
        cgPath = CGMutablePath()
        cgPath.closeSubpath()
        cgPath.addRelativeArc(center: viewCenter, radius: viewCenter.x-lineTop.x
            , startAngle: (CGFloat(3*Double.pi/2)+angle/2), delta: -angle)
        
        cgPath.addRelativeArc(center: viewCenter, radius: (viewCenter.x-lineTop
            .x)+circleBorderWidth/3, startAngle: (CGFloat(3*Double.pi/2)-angle/3)
            , delta: angle*0.67)
        cgPath.addRelativeArc(center: viewCenter, radius: viewCenter.x-lineTop.x
            , startAngle: (CGFloat(3*Double.pi/2)+angle/2), delta: -angle)
        let currentPoint = cgPath.currentPoint
        
        let frameTopRect = CGRect(x: currentPoint.x-circleBorderWidth-circleBorderWidth/4
            , y: currentPoint.y-circleBorderWidth-circleBorderWidth/3, width:
            circleBorderWidth+circleBorderWidth/2, height: circleBorderWidth)
        path = UIBezierPath(roundedRect: frameTopRect, cornerRadius: circleBorderWidth/5)
        
        /* Initially render rectangle, then render bottom view.
         to avoid shadow effect on bottom view */
        context.addPath(path.cgPath)
        context.setShadow(offset: shadowOffset, blur: shadowBlur, color: shadowColor.cgColor)
        context.fillPath(using: .evenOdd)
        context.setFillColor(fillColor.cgColor)
        context.fillPath()
        path.removeAllPoints()
        
        context.addPath(cgPath)
        context.setShadow(offset: shadowOffset, blur: shadowBlur, color: shadowColor.cgColor)
        context.fillPath(using: .evenOdd)
        context.setFillColor(fillColor.cgColor)
        context.fillPath()
    }
}
