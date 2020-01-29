/*//////////////////////////////////////////////////////////////////////////////
 //
 //    Copyright (c) GreenPrint Technologies LLC. 2019
 //
 //    File Name         : Extensions.swift
 //
 //    File Created      : 19:Sept:2019
 //
 //    Dev Name          : Sachin Kumar K.
 //
 //    Description       : All Extensions to Foundation and UIKit classes.
 //
 //////////////////////////////////////////////////////////////////////////// */

import Foundation
import UIKit
import QuartzCore

extension UILabel {
    /// To provide underline for text field.
    public func useUnderline(uiColor: UIColor = .white) {
        let border = CALayer()
        let borderWidth = CGFloat(1.0)
        border.borderColor = uiColor.cgColor
        border.frame = CGRect(origin: CGPoint(x: 0,y :self.frame.size.height ),
                              size: CGSize(width: self.frame.size.width, height:  1))
        border.borderWidth = borderWidth
        self.layer.addSublayer(border)
        self.layer.masksToBounds = false
    }
    
    /// Shake text field.
    func shakeLabel()
    {
        let animation = CABasicAnimation(keyPath: "position")
        animation.duration = 0.07
        animation.repeatCount = 3
        animation.autoreverses = true
        animation.fromValue = NSValue(cgPoint: CGPoint(x: self.center.x - 10, y:
            self.center.y))
        animation.toValue = NSValue(cgPoint: CGPoint(x: self.center.x + 10, y:
            self.center.y))
        self.layer.add(animation, forKey: "position")
    }
}

extension UITextField {
    /// To provide underline for text field.
    public func useUnderline(uiColor: UIColor = .white) {
        let border = CALayer()
        let borderWidth = CGFloat(1.0)
        border.borderColor = uiColor.cgColor
        border.frame = CGRect(origin: CGPoint(x: 0,y :self.frame.size.height ),
                              size: CGSize(width: self.frame.size.width, height:  1))
        border.borderWidth = borderWidth
        self.layer.addSublayer(border)
        self.layer.masksToBounds = false
    }
    
    /// Shake text field.
    func shakeTextField()
    {
        let animation = CABasicAnimation(keyPath: "position")
        animation.duration = 0.07
        animation.repeatCount = 3
        animation.autoreverses = true
        animation.fromValue = NSValue(cgPoint: CGPoint(x: self.center.x - 10, y:
            self.center.y))
        animation.toValue = NSValue(cgPoint: CGPoint(x: self.center.x + 10, y:
            self.center.y))
        self.layer.add(animation, forKey: "position")
    }
}

extension UITextView {
    /// Draw underline to textview.
    public func useUnderline(uiColor: UIColor = .white) {
        let border = CALayer()
        let borderWidth = CGFloat(1.0)
        border.borderColor = uiColor.cgColor
        border.frame = CGRect(origin: CGPoint(x: 0,y :self.frame.size.height ),
                              size: CGSize(width: self.frame.size.width, height:  1))
        border.borderWidth = borderWidth
        self.layer.addSublayer(border)
        self.layer.masksToBounds = false
    }
}

extension UIScrollView {
    func scrollToTop() {
        let desiredOffset = CGPoint(x: 0, y: -contentInset.top)
        setContentOffset(desiredOffset, animated: true)
    }
    
    func scrollToBottom() {
        if self.contentSize.height < self.bounds.size.height { return }
        let bottomOffset = CGPoint(x: 0, y: self.contentSize.height - self.bounds.size.height)
        self.setContentOffset(bottomOffset, animated: true)
    }
}

extension UIButton {
    /// Applies gradient color to a button in round.
    public func drawRounded() {
        self.addGradient(cgFRadius: self.bounds.height / 2)
        self.layer.cornerRadius = self.frame.width / 2
        self.layer.shadowColor = UIColor.gray.cgColor
        self.layer.shadowOffset = CGSize(width: 1.5, height: 1.5)
        self.layer.shadowRadius = 3
        self.layer.shadowOpacity = 0.6
        self.layer.masksToBounds = false
    }
    
    /// Draw rectangle inside button.
    ///
    /// - Parameters:
    ///    - layerHeight: represents width and height.
    public func drawStop(layerHeight: CGFloat) {
        //To draw a rectangle representing stop condition inside view or button.
        let cgRect = CGRect(x: 0, y: 0, width: layerHeight, height: layerHeight)
        let cgSize = CGSize(width: cgRect.width / 10, height: cgRect.width / 10)
        let cgCPosition = CGPoint(x: frame.width * 0.5 - cgRect.width / 2, y:
            frame.width * 0.5 - cgRect.width / 2)
        let cgPRoundRect = UIBezierPath(roundedRect: cgRect, byRoundingCorners:
            .allCorners, cornerRadii: cgSize)
        let shape = CAShapeLayer()
        shape.path = cgPRoundRect.cgPath
        shape.fillColor = UIColor.white.cgColor
        shape.position = cgCPosition
        layer.mask = shape
        
        // Remove previosly added layer (Play icon layer)
        if layer.sublayers!.count > 1 {
            layer.sublayers!.remove(at: 1)
        }
        layer.insertSublayer(shape, at: 1)
    }
    
    /// Draw play icon inside button.
    ///
    /// - Parameters:
    ///    - layerHeight: represents height .
    ///    - radius: default value = 2
    public func drawPlay(layerHeight: CGFloat, radius: CGFloat = 2) {
        // 3 Points to draw triangle.
        let point1 = CGPoint(x: 0, y: layerHeight)
        let point2 = CGPoint(x: layerHeight, y: layerHeight / 2)
        let point3 = CGPoint(x: 0, y: 0)
        
        let path = CGMutablePath()
        path.move(to: CGPoint(x: 0, y: 0))
        path.addArc(tangent1End: point1, tangent2End: point2, radius: radius)
        path.addArc(tangent1End: point2, tangent2End: point3, radius: radius)
        path.addArc(tangent1End: point3, tangent2End: point1, radius: radius)
        path.closeSubpath()
        
        let uiBez = UIBezierPath(cgPath: path)
        let cgCPosition = CGPoint(x: frame.width * 0.5 - layerHeight / 3,
                                  y: frame.width * 0.5 - layerHeight / 2)
        // Mask to Path
        let shape = CAShapeLayer()
        shape.path = uiBez.cgPath
        shape.fillColor = UIColor.white.cgColor
        shape.position = cgCPosition
        layer.mask = shape
        
        //Remove layer if any (Stop icon layer)
        if layer.sublayers!.count > 1 {
            layer.sublayers!.remove(at: 1)
        }
        layer.insertSublayer(shape, at: 1)
    }
}

extension UITableView {
    /// Rounded corners to table view
    ///
    /// - parameters:
    ///   - corners: Specify UIRectCorners to add rounded corner.
    ///   - radius: Corner radius.
    public func roundCorners(corners: UIRectCorner, radius: CGFloat){
        if #available(iOS 11.0, *) {
            clipsToBounds = true
            layer.cornerRadius = radius
            layer.maskedCorners = CACornerMask(rawValue: corners.rawValue)
        } else {
            layer.cornerRadius = 35
            layer.masksToBounds = true
        }
    }
}

extension UILabel {
    /// Animation while changing the font size.
    public func animateToFont(_ font: UIFont, withDuration duration: TimeInterval) {
        let oldFont = self.font
        self.font = font
        let labelScale = oldFont!.pointSize / font.pointSize
        let oldTransform = transform
        transform = transform.scaledBy(x: labelScale, y: labelScale)
        setNeedsUpdateConstraints()
        UIView.animate(withDuration: duration) {
            self.transform = oldTransform
            self.layoutIfNeeded()
        }
    }
}

extension UITableView {
    /// Reload tableview data.
    ///
    /// - parameters:
    ///   - completion: closure will handle completion handler.
    public func reloadData(completion: @escaping () -> ()) {
        UIView.animate(withDuration: 0, animations: { self.reloadData()})
        {_ in
            completion() }
    }
    
    /// Animation while reloading table view data. (With fade and 0.3sec time)
    public func reloadDataWithAnimation() {
        let transition = CATransition()
        transition.type = CATransitionType.fade
        transition.timingFunction = CAMediaTimingFunction(name: CAMediaTimingFunctionName.easeIn)
        transition.fillMode = CAMediaTimingFillMode.forwards
        transition.duration = 0.3
        transition.subtype = CATransitionSubtype.fromTop
        self.layer.add(transition, forKey: "UITableViewReloadDataAnimationKey")
        // Update your data source here
        self.reloadData()
    }
}

extension UIView {
    
    /// Draw a line.
    func addLine(rect: CGRect? = nil) {
        self.layer.masksToBounds = false
        let border = CALayer()
        let borderWidth = CGFloat(0.25)
        border.borderColor = g_colorMode.lineColor().withAlphaComponent(0.5).cgColor
        if nil != rect {
            border.frame = rect!
        }
        else {
            let width = UIScreen.main.bounds.width
            border.frame = CGRect(x: 20, y: bounds.maxY-1, width: width, height: 1)
        }
        border.borderWidth = borderWidth
        self.layer.addSublayer(border)
    }
    
    /// To add gradient to the view.
    func addGradient(cgPStart: CGPoint = CGPoint(x: 0,
        y: 0), cgPEnd: CGPoint = CGPoint(x: 1, y: 1), cgFRadius: CGFloat = 0) {
        
        let startColor = g_colorMode.startColor()
        let endColor = g_colorMode.endColor()
        
        let gradient = CAGradientLayer()
        gradient.frame = CGRect(x: 0, y: cgPStart.y*bounds.height / 2,
                                width: bounds.width, height: cgPEnd.y * bounds.height)
        gradient.cornerRadius = cgFRadius
        gradient.colors = [startColor, endColor]
        gradient.startPoint = cgPStart
        gradient.endPoint = cgPEnd
        gradient.drawsAsynchronously = true
        layer.insertSublayer(gradient, at: 0)
    }
    
    /// To add gradient to the view.
    func addGradientCell(color1: CGColor, color2: CGColor, cgPStart: CGPoint = CGPoint(x: 0.5
        ,y: 0.5), cgPEnd: CGPoint = CGPoint(x: 1, y: 1), cgFRadius: CGFloat = 0) {
        
        let startColor = color1
        let endColor = color2
        
        let gradient = CAGradientLayer()
        gradient.frame = CGRect(x: 0, y: cgPStart.y*bounds.height / 2,
                                width: bounds.width, height: cgPEnd.y * bounds.height)
        gradient.cornerRadius = cgFRadius
        gradient.colors = [startColor, endColor]
        gradient.startPoint = cgPStart
        gradient.endPoint = cgPEnd
        gradient.drawsAsynchronously = true
        layer.insertSublayer(gradient, at: 0)
    }
    
    /// Draw shadow to view.
    public func drawShadow() {
        let border = CALayer()
        let borderWidth = CGFloat(0.25)
        border.borderColor = UIColor.lightGray.withAlphaComponent(0.5).cgColor
        let width = UIScreen.main.bounds.width
        border.frame = CGRect(origin: CGPoint(x: 0,y :self.frame.size.height ),
                              size: CGSize(width: width, height:  1))
        border.borderWidth = borderWidth
        self.layer.addSublayer(border)
        self.layer.masksToBounds = false
    }
    
    /// Draw drop shadow to view.
    func dropShadow(shadowColor: UIColor) {
        layer.masksToBounds = false
        layer.shadowColor = shadowColor.cgColor
        layer.shadowOpacity = 0.5
        layer.shadowOffset = CGSize(width: 0, height: 1)
        layer.shadowRadius = 1
        
        let cgRect = CGRect(origin: CGPoint(x: 0,y :self.frame.size.height ),
                            size: CGSize(width: self.frame.size.width, height:  1))
        layer.shadowPath = UIBezierPath(rect: cgRect).cgPath
//        layer.shouldRasterize = true
    }
    
    /// Draw shadow from bezierpath.
    func drawShadowFromBzrPath() {
        clipsToBounds = false
        layer.masksToBounds = false
        self.layer.shadowRadius = 2
        self.layer.shadowOpacity = 0.4
        layer.shadowColor = g_colorMode.invertColor().cgColor
        let cgRect = CGRect(x: bounds.minX, y: bounds.maxY, width: bounds.width, height: 2)
        self.layer.shadowPath = UIBezierPath(rect: cgRect).cgPath
    }
    
    /// Add inside shadow
    func addInsideShadow(to edges: [UIRectEdge], radius: CGFloat = 2.0, opacity: Float = 0.4
        , color: CGColor = g_colorMode.defaultColor().withAlphaComponent(0.5).cgColor) {
        
        let fromColor = color
        let toColor = UIColor.clear.cgColor
        let viewFrame = self.frame
        for edge in edges {
            let gradientLayer = CAGradientLayer()
            gradientLayer.colors = [fromColor, toColor]
            gradientLayer.opacity = opacity
            
            switch edge {
                case .top:
                    gradientLayer.startPoint = CGPoint(x: 0.5, y: 0.0)
                    gradientLayer.endPoint = CGPoint(x: 0.5, y: 1.0)
                    gradientLayer.frame = CGRect(x: 0.0, y: 0.0, width: viewFrame.width
                        , height: radius)
                case .bottom:
                    gradientLayer.startPoint = CGPoint(x: 0.5, y: 1.0)
                    gradientLayer.endPoint = CGPoint(x: 0.5, y: 0.0)
                    gradientLayer.frame = CGRect(x: 0.0, y: viewFrame.height - radius
                        , width: viewFrame.width, height: radius)
                case .left:
                    gradientLayer.startPoint = CGPoint(x: 0.0, y: 0.5)
                    gradientLayer.endPoint = CGPoint(x: 1.0, y: 0.5)
                    gradientLayer.frame = CGRect(x: 0.0, y: 0.0, width: radius
                        , height: viewFrame.height)
                case .right:
                    gradientLayer.startPoint = CGPoint(x: 1.0, y: 0.5)
                    gradientLayer.endPoint = CGPoint(x: 0.0, y: 0.5)
                    gradientLayer.frame = CGRect(x: viewFrame.width - radius, y: 0.0
                        , width: radius, height: viewFrame.height)
                default:
                    break
            }
            self.layer.addSublayer(gradientLayer)
        }
    }
    
    /// Add inverse shadow.
    func inverseShadowToTop() {
        clipsToBounds = false
        layer.masksToBounds = false
        self.layer.shadowRadius = 2
        self.layer.shadowOffset = CGSize(width: 0, height: 1)
        self.layer.shadowOpacity = 0.2
        layer.shadowColor = g_colorMode.invertColor().cgColor
        let cgRect = CGRect(x: bounds.minX, y: bounds.minY, width: bounds.width, height: 2)
        self.layer.shadowPath = UIBezierPath(rect: cgRect).cgPath
    }
    
    func addRightSideLine(uiColor: UIColor = .lightGray) {
        let border = CALayer()
        let borderWidth = CGFloat(1.0)
        border.borderColor = uiColor.withAlphaComponent(0.5).cgColor
        border.frame = CGRect(origin: CGPoint(x: self.frame.size.width,y :0),
                              size: CGSize(width: 1, height:  self.frame.size.height))
        border.borderWidth = borderWidth
        self.layer.addSublayer(border)
        self.layer.masksToBounds = false
    }
}

extension Date {
    /// Milli second value from "timeIntervalSince1970" contains date and time.
    ///  - Data type: Int64
    var millisecondsSince1970: Int64 {
        return Int64((self.timeIntervalSince1970).rounded())
    }
    
//    var millisecondsSince1970: Int64 {
//        return Int64((self.timeIntervalSince1970).rounded()) +
//            Int64(TimeZone.current.secondsFromGMT())
//    }
    
    /// Initialise date object from millisecondsSince1970 values.
    init(milliseconds:Int64) {
        self = Date(timeIntervalSince1970: TimeInterval(milliseconds))
    }
    
    /// From dd/MM/yyyy HH:mm:ss
    init(strDateTime: String) {
        let dateFormatter = DateFormatter()
        dateFormatter.dateFormat = "dd/MM/yyyy HH:mm:ss" //Your date format
        //    dateFormatter.timeZone = TimeZone(abbreviation: "GMT+0:00") //Current time zone
        //according to date format your date string
        self = dateFormatter.date(from: strDateTime)!
    }
    
    /// Compare two dates are equal are not.
    func compare(to secSince1970: Int64) -> Bool {
        let dateCompare = Date(milliseconds: secSince1970)
        let dateToday = Date()
        let formatter = DateFormatter()
        formatter.dateFormat = "dd/MM/yyyy"
        let firstDate = formatter.string(from: dateToday)
        let secondDate = formatter.string(from: dateCompare)
        
        if firstDate.compare(secondDate) == .orderedSame {
            return true
        }
        else {
            return false
        }        
    }
    
    /// To get time from date object.
    var timeInDate: Int {
        let dayStartTime = startOftheDay().millisecondsSince1970
        let timeNow = self.millisecondsSince1970
        return Int((timeNow - dayStartTime))
    }
    
    var day: Int {
        let calendar = Calendar.current
        let secDate = self.millisecondsSince1970 - Int64(TimeZone.current.secondsFromGMT())
        let date = Date(milliseconds: secDate)
        let components = calendar.dateComponents([.day], from: date)
        return components.day!
    }
    
    var mon: Int {
        let calendar = Calendar.current
        let secDate = self.millisecondsSince1970 - Int64(TimeZone.current.secondsFromGMT())
        let date = Date(milliseconds: secDate)
        let components = calendar.dateComponents([.month], from: date)
        return components.month!
    }
    
    var year: Int {
        let calendar = Calendar.current
        let secDate = self.millisecondsSince1970 - Int64(TimeZone.current.secondsFromGMT())
        let date = Date(milliseconds: secDate)
        let components = calendar.dateComponents([.year], from: date)
        return components.year!
    }
    
    /// To get date string in dd/MM/yyyy format.
    func getStrDate(from secSince1970: Int64 = Date().millisecondsSince1970) -> String {
        let calendar = Calendar.current
        let secDate = secSince1970 - Int64(TimeZone.current.secondsFromGMT())
        let date = Date(milliseconds: secDate)
        let components = calendar.dateComponents([.year, .month, .day], from: date)
        var strDate: String!
        var strMonth: String!
        if components.day! < 10 {
            strDate = "0\(components.day!)"
        }
        else {
            strDate = "\(components.day!)"
        }
        if components.month! < 10 {
            strMonth = "0\(components.month!)"
        }
        else {
            strMonth = "\(components.month!)"
        }
        return "\(strDate!)/\(strMonth!)/\(components.year!)"
    }
    
    func getDateFromStrDateAndIntTime(strDate: String, nTime: Int) -> Date {
        let dateFormatter = DateFormatter()
        let strTime = getSecondsToHourMinute(seconds: nTime)
        dateFormatter.dateFormat = "dd/MM/yyyy HH:mm"
        guard let date = dateFormatter.date(from: "\(strDate) \(strTime)") else {
            fatalError()
        }
        return date
    }
    
    /// Get string date in dd/MM/yyyy format.
    func getStrDate() -> String {
        let calendar = Calendar.current
        let components = calendar.dateComponents([.year, .month, .day], from: self)
        var strDate: String!
        var strMonth: String!
        if components.day! < 10 {
            strDate = "0\(components.day!)"
        }
        else {
            strDate = "\(components.day!)"
        }
        if components.month! < 10 {
            strMonth = "0\(components.month!)"
        }
        else {
            strMonth = "\(components.month!)"
        }
        return "\(strDate!)/\(strMonth!)/\(components.year!)"
    }
    
    /// Get string date in dd/MM/yyyy format.
    func getStrTimeFormat2() -> String {
        let calendar = Calendar.current
        let components = calendar.dateComponents([.hour, .minute, .second], from: self)
        var strHr: String!
        var strMin: String!
        var strSec: String!
        if components.hour! < 10 {
            strHr = "0\(components.hour!)"
        }
        else {
            strHr = "\(components.hour!)"
        }
        if components.minute! < 10 {
            strMin = "0\(components.minute!)"
        }
        else {
            strMin = "\(components.minute!)"
        }
        if components.second! < 10 {
            strSec = "0\(components.second!)"
        }
        else {
            strSec = "\(components.second!)"
        }
        
        return "\(strHr!):\(strMin!):\(strSec!)"
    }
    
    /// Date object contains current date mid night value.
    func startOftheDay() -> Date {
        var calendar = Calendar.current
        calendar.timeZone = TimeZone(secondsFromGMT: 0)!
        var startOfComponent = self
        var timeInterval : TimeInterval = 0.0
        _ = calendar.dateInterval(of: .day, start: &startOfComponent, interval: &timeInterval, for: self)
        return startOfComponent
    }
    
    /// format h:mm a
    func getStrTime() -> String {
        let dateF = DateFormatter()
        dateF.dateFormat = "h:mm a"
        let strTime = dateF.string(from: self)
        return strTime
    }
    
    /// returns string month
    var month: String {
        let dateFormatter = DateFormatter()
        dateFormatter.dateFormat = "MMM"
        return dateFormatter.string(from: self)
    }
    
    /// Get time difference of object and and a millisecondsSince1970 value.
    func getTimeDifference(from secSince1970: Int64) -> Int {
        let startDate = self.millisecondsSince1970
        let difference = secSince1970 - startDate
        return Int(difference)
    }
    
    /// Returns the amount of years from another date.
    func years(from date: Date) -> Int {
        return Calendar.current.dateComponents([.year], from: date, to: self).year ?? 0
    }
    /// Returns the amount of months from another date.
    func months(from date: Date) -> Int {
        return Calendar.current.dateComponents([.month], from: date, to: self).month ?? 0
    }
    /// Returns the amount of weeks from another date.
    func weeks(from date: Date) -> Int {
        return Calendar.current.dateComponents([.weekOfMonth], from: date, to: self).weekOfMonth ?? 0
    }
    /// Returns the amount of days from another date.
    func days(from date: Date) -> Int {
        return Calendar.current.dateComponents([.day], from: date, to: self).day ?? 0
    }
}

extension UIButton {
    var height: CGFloat {
        get {
            return self.frame.size.height
        }
        set {
            self.frame.size.height = newValue
        }
    }
    
    var y:CGFloat {
        get {
            return self.frame.origin.y
        }
        set {
            self.frame.origin.y = newValue
        }
    }
    
    /// Apply rounded corners to top.
    func topRounded(){
        if #available(iOS 11.0, *) {
            clipsToBounds = true
            layer.cornerRadius = 3
            layer.maskedCorners = [.layerMinXMinYCorner, .layerMaxXMinYCorner]
        } else {
            // Fallback on earlier versions
        }
    }
}

extension Dictionary where Value: Equatable {
    func getKey(forValue val: Value) -> Key? {
        return first(where: { $1 == val })?.key
    }
}

extension UIColor {
    /// Possible initialization with
    /// UIColor(hexString: "#00F")
    /// UIColor(hexString: "#0000FF")
    /// UIColor(hexString: "#FF0000FF")
    convenience init?(hexString: String) {
        var chars = Array(hexString.hasPrefix("#") ? hexString.dropFirst() : hexString[...])
        let red, green, blue, alpha: CGFloat
        switch chars.count {
            case 3:
                chars = chars.flatMap { [$0, $0] }
                fallthrough
            case 6:
                chars = ["F","F"] + chars
                fallthrough
            case 8:
                alpha = CGFloat(strtoul(String(chars[0...1]), nil, 16)) / 255
                red   = CGFloat(strtoul(String(chars[2...3]), nil, 16)) / 255
                green = CGFloat(strtoul(String(chars[4...5]), nil, 16)) / 255
                blue  = CGFloat(strtoul(String(chars[6...7]), nil, 16)) / 255
            default:
                return nil
        }
        self.init(red: red, green: green, blue:  blue, alpha: alpha)
    }
}

extension UIApplication {
    /// To get presenting view controller any where in the app.
    class func topViewController(controller: UIViewController? = UIApplication.shared.keyWindow?
        .rootViewController) -> UIViewController? {
        if let navigationController = controller as? UINavigationController {
            return topViewController(controller: navigationController.visibleViewController)
        }
        if let tabController = controller as? UITabBarController {
            if let selected = tabController.selectedViewController {
                return topViewController(controller: selected)
            }
        }
        if let presented = controller?.presentedViewController {
            return topViewController(controller: presented)
        }
        return controller
    }
}

extension Array {
    func shift(withDistance distance: Int = 1) -> Array<Element> {
        let offsetIndex = distance >= 0 ?
            self.index(startIndex, offsetBy: distance, limitedBy: endIndex) :
            self.index(endIndex, offsetBy: distance, limitedBy: startIndex)
        
        guard let index = offsetIndex else { return self }
        return Array(self[index ..< endIndex] + self[startIndex ..< index])
    }
    
    mutating func shiftInPlace(withDistance distance: Int = 1) {
        self = shift(withDistance: distance)
    }
    
}
