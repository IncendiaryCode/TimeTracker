/*//////////////////////////////////////////////////////////////////////////////
 //
 //    Copyright (c) GreenPrint Technologies LLC. 2019
 //
 //    File Name         : UserguideView.swift
 //
 //    File Created      : 16:Dec:2019
 //
 //    Dev Name          : Sachin Kumar K.
 //
 //    Description       : User guide view.
 //
 //////////////////////////////////////////////////////////////////////////// */

import UIKit

class UserguideView: UIView {
    var btnNext: UIButton!
    var btnSkip: UIButton!
    var viewHighlight: UIView!
    var txtVHint: UITextView!
    var pageCtrlr: UIPageControl!
    var arrUserguideData: Array<UserguideData>!
    var completionHandler: (()->Void)?
    var currentPage: Int! {
        didSet {
            // Call whenever current page number changed.
            changePage()
        }
    }
    
    override var frame: CGRect {
        didSet {
            setupFrames()
        }
    }
    
    func setupFrames() {
        let viewFrame = self.frame
        let btnNextFrame = CGRect(x: viewFrame.maxX - 100,
                                  y: viewFrame.maxY - 84,
                                  width: 80,
                                  height: 44)
        // To avoid awake calling.
        if nil != btnNext {
            btnNext.frame = btnNextFrame
            pageCtrlr.center = CGPoint(x: viewFrame.midX, y: btnNextFrame.minY-20)
        }
    }
    
    private func setup() {
        createOpacityView()
        self.clipsToBounds = true
        let viewFrame = self.frame
        let btnNextFrame = CGRect(x: viewFrame.maxX - 100,
                              y: viewFrame.maxY - 84,
                              width: 80,
                              height: 44)
        btnNext = UIButton(frame: btnNextFrame)
        btnNext.setTitle("Next", for: .normal)
        let cgPStart = CGPoint(x: 0, y: 0.25)
        let cgPEnd = CGPoint(x: 1, y: 0.75)
        btnNext.addGradient(cgPStart: cgPStart, cgPEnd: cgPEnd, cgFRadius: 15)
        btnNext.addTarget(self, action: #selector(btnNextPressed), for: .touchUpInside)
        self.addSubview(btnNext)
        
        let btnSkipFrame = CGRect(x: 20,
                                  y: 40,
                                  width: 60,
                                  height: 44)
        btnSkip = UIButton(frame: btnSkipFrame)
        btnSkip.setTitle("Skip", for: .normal)
        btnSkip.addGradient(cgPStart: cgPStart, cgPEnd: cgPEnd, cgFRadius: 15)
        btnSkip.addTarget(self, action: #selector(btnSkipPressed), for: .touchUpInside)
        self.addSubview(btnSkip)
        
        viewHighlight = UIView()
        self.addSubview(viewHighlight)
        
        txtVHint = UITextView()
        txtVHint.text = arrUserguideData[0].itemHint
        txtVHint.textAlignment = .center
        txtVHint.font = .systemFont(ofSize: 16)
        txtVHint.translatesAutoresizingMaskIntoConstraints = true
        txtVHint.sizeToFit()
        txtVHint.isUserInteractionEnabled = false
        
        txtVHint.textColor = .white
        txtVHint.backgroundColor = .clear
        self.addSubview(txtVHint)
        
        // Add page controller to the view.
        pageCtrlr = UIPageControl()
        pageCtrlr.center = CGPoint(x: viewFrame.midX, y: btnNextFrame.minY-20)
        pageCtrlr.numberOfPages = arrUserguideData.count
        pageCtrlr.pageIndicatorTintColor = .black
        pageCtrlr.isUserInteractionEnabled = false
        pageCtrlr.hidesForSinglePage = true
        self.addSubview(pageCtrlr)
        currentPage = 0
    }
    
    /// Call initialiser when it is only one guideline.
    init(userguideData: UserguideData) {
        // Set full screen view.
        super.init(frame: UIScreen.main.bounds)
        self.arrUserguideData = Array<UserguideData>()
        self.arrUserguideData.append(userguideData)
        setup()
    }
    
    override init(frame: CGRect) {
        super.init(frame: frame)
    }
    
    required init(coder aDecoder: NSCoder) {
        fatalError("init(coder:) has not been implemented")
    }
    
    func changePage() {
        // If last page.
        if currentPage > arrUserguideData.count-1 {
            self.removeFromSuperview()
        }
        else {
            if currentPage == arrUserguideData.count-1 {
                btnSkip.isHidden = true
                btnNext.setTitle("Done", for: .normal)
            }
            
            viewHighlight.frame = arrUserguideData[currentPage].itemFrame
            txtVHint.text = arrUserguideData[currentPage].itemHint
            
            let hintPoint = calculateTextHintFrame(for: arrUserguideData[currentPage].itemFrame)
            var hintFrame = CGRect(x: hintPoint.x, y: hintPoint.y, width: 250, height: 70)
            //Set height based on contentsize.
            hintFrame.size.height = txtVHint.contentSize.height
            txtVHint.frame = hintFrame
                        
            applyAlpha()
    
            // If hintpoint below highlight area.
            if hintPoint.y > viewHighlight.frame.minY {
                addArrow(start: point(txtVHint.frame.midX, txtVHint.frame.minY)
                    , end: point(viewHighlight.frame.midX, viewHighlight.frame.maxY))
            }
            else {
            // If highlight below hintpoint area.
                addArrow(start: point(viewHighlight.frame.midX, viewHighlight.frame.minY)
                    , end: point(txtVHint.frame.midX, txtVHint.frame.maxY))
            }
            pageCtrlr.currentPage = currentPage
        }
    }
    
    @objc func btnNextPressed(sender: UIButton!) {
        if currentPage < arrUserguideData.count-1 {
            currentPage += 1
        }
        else {
            if let handler = completionHandler {
                handler()
            }
            self.removeFromSuperview()
        }
    }
    
    @objc func btnSkipPressed(sender: UIButton!) {
        currentPage = arrUserguideData.count
    }
    
    /// Call initialiser when it is array of guideline.
    init(arrUserguideData: Array<UserguideData>) {
        // Set full screen view.
        super.init(frame: UIScreen.main.bounds)
        self.arrUserguideData = Array<UserguideData>()
        for userguideData in arrUserguideData {
            self.arrUserguideData.append(userguideData)
        }
        setup()
    }
    
    override func draw(_ rect: CGRect) {
        // Drawing code
    }
    
    /// Calculates appropriate position to fit hint label in the view for highlighted area.
    private func calculateTextHintFrame(for itemFrame: CGRect) -> CGPoint {
        let screenFrame = UIScreen.main.bounds
        
        // If items maximum y less than middle y of screen.
        if itemFrame.maxY < screenFrame.midY {
            return CGPoint(x: (screenFrame.width-250)/2, y: itemFrame.maxY + 60)
        }
        else {
            return CGPoint(x: (screenFrame.width-250)/2, y: itemFrame.minY - 110)
        }
    }
    
    /// Create four views to apply opacity except highlight reagion.
    private func createOpacityView() {
        let viewAlpha1 = UIView()
        let viewAlpha2 = UIView()
        let viewAlpha3 = UIView()
        let viewAlpha4 = UIView()

        // Apply alpha.
        viewAlpha1.backgroundColor = UIColor.black.withAlphaComponent(0.8)
        viewAlpha2.backgroundColor = UIColor.black.withAlphaComponent(0.8)
        viewAlpha3.backgroundColor = UIColor.black.withAlphaComponent(0.8)
        viewAlpha4.backgroundColor = UIColor.black.withAlphaComponent(0.8)
        
        // Set tag for future reference.
        viewAlpha1.tag = 1
        viewAlpha2.tag = 2
        viewAlpha3.tag = 3
        viewAlpha4.tag = 4

        self.addSubview(viewAlpha1)
        self.addSubview(viewAlpha2)
        self.addSubview(viewAlpha3)
        self.addSubview(viewAlpha4)
    }
    
    
    /// To apply alpha to the view except hightlight region.
    private func applyAlpha() {
        let exceptFrame = arrUserguideData[currentPage].itemFrame!
        let screenBounds = UIScreen.main.bounds
        
        let frameOne = CGRect(x: 0,
                              y: 0,
                              width: screenBounds.width,
                              height: exceptFrame.minY)
        let frameTwo = CGRect(x: 0,
                              y: exceptFrame.minY,
                              width: exceptFrame.minX,
                              height: exceptFrame.height)
        let frameThree = CGRect(x: exceptFrame.maxX,
                                y: exceptFrame.minY,
                                width: (screenBounds.width-exceptFrame.maxX),
                                height: exceptFrame.height)
        let frameFour = CGRect(x: 0,
                               y: exceptFrame.maxY,
                               width: screenBounds.width,
                               height:(screenBounds.height-exceptFrame.minY))
        
        let viewAlpha1 = self.viewWithTag(1)!
        let viewAlpha2 = self.viewWithTag(2)!
        let viewAlpha3 = self.viewWithTag(3)!
        let viewAlpha4 = self.viewWithTag(4)!

        viewAlpha1.frame = frameOne
        viewAlpha2.frame = frameTwo
        viewAlpha3.frame = frameThree
        viewAlpha4.frame = frameFour
    }
    
    /// Initialise CGPoint variable.
    private func point(_ x: CGFloat, _ y: CGFloat) -> CGPoint {
        return CGPoint(x: x, y: y)
    }

    private func addArrow(start: CGPoint, end: CGPoint) {
        if self.layer.sublayers!.count > 9 {
            self.layer.sublayers?.removeLast()
        }
        
        // Create path.
        let path = UIBezierPath()
        path.move(to: start)
        path.addCurve(to: end, controlPoint1: point(start.x, end.y)
            , controlPoint2: point(end.x, start.y))
        // If hintpoint below highlight area.
        if txtVHint.frame.minY > viewHighlight.frame.minY {
            path.move(to: start)
            path.addLine(to: point(start.x-10,start.y-10))
            path.addLine(to: start)
            path.addLine(to: point(start.x+10,start.y-10))
        }
        else {
            path.move(to: end)
            path.addLine(to: point(end.x-10,end.y+10))
            path.addLine(to: end)
            path.addLine(to: point(end.x+10,end.y+10))
        }
        
        // Render using shape layer.
        let shapeLayer = CAShapeLayer()
        shapeLayer.path = path.cgPath
        shapeLayer.strokeColor = g_colorMode.midColor().cgColor
        shapeLayer.fillColor = UIColor.clear.cgColor
        shapeLayer.lineWidth = 3.0
        self.layer.addSublayer(shapeLayer)
    }
}

struct UserguideData {
    var itemFrame: CGRect!
    var itemHint: String!
    
    init(itemFrame: CGRect, itemHint: String) {
        self.itemHint = itemHint
        self.itemFrame = itemFrame
    }
}
