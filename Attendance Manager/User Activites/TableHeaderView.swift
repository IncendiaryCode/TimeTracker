//
//  TableHeaderView.swift
//  Attendance Manager
//
//  Created by Sachin on 9/9/19.
//  Copyright © 2019 Sachin. All rights reserved.
//

protocol TableHeaderViewDelegate {
    func toogleSection(section: Int)
}

import UIKit

class TableHeaderView: UITableViewHeaderFooterView {
    var delegate: TableHeaderViewDelegate?
    var nSection: Int!
    var lblTitle: UILabel!
    var btnFilter: UIButton!
    var lblFilterShow: UILabel!
    override init(reuseIdentifier: String?) {
        super.init(reuseIdentifier: reuseIdentifier)
//        self.addGestureRecognizer(UITapGestureRecognizer(target: self,
//                                                         action: #selector(selectheaderAction)))
    }
    
    required init?(coder aDecoder: NSCoder) {
        fatalError("init(coder:) has not been implemented")
    }
    
//    @objc func selectheaderAction(gestureRecognizer: UITapGestureRecognizer) {
//        delegate?.toogleSection(section: self.nSection)
//    }
    
    func customInit(title: String, section: Int, delegate: TableHeaderViewDelegate) {
        var cgRValue: CGRect!
        let cgFScreenWidth = UIScreen.main.bounds.width
        cgRValue = CGRect(x: cgFScreenWidth / 3, y: 42, width:
            cgFScreenWidth / 2 - cgFScreenWidth / 6, height: 21)
        self.layer.masksToBounds = true
        self.layer.cornerRadius = 15
        self.layer.backgroundColor = UIColor.white.cgColor
        lblTitle = UILabel(frame: cgRValue)
        lblTitle.textAlignment = .center
        lblTitle.textColor = .lightGray
        lblTitle.font = lblTitle.font.withSize(12)
        self.addSubview(lblTitle)
        
        cgRValue = CGRect(x: cgFScreenWidth - cgFScreenWidth * 0.15 ,
                          y: lblTitle.frame.minY, width: 20, height: 20)
        btnFilter = UIButton(frame: cgRValue)
        btnFilter.backgroundColor = .clear
        btnFilter.setImage(#imageLiteral(resourceName: "FilterIcon"), for: .normal)
        btnFilter.alpha = 0.4
        self.addSubview(btnFilter)
        self.nSection = section
        self.delegate = delegate
        self.lblTitle.text = title
        
        cgRValue =  CGRect(x: cgFScreenWidth - cgFScreenWidth * 0.15 + 20,
                           y: lblTitle.frame.minY, width: 5, height: 5)
        
        lblFilterShow = UILabel(frame: cgRValue)
        lblFilterShow.layer.masksToBounds = true
        lblFilterShow.layer.cornerRadius = 2.5
        self.addSubview(lblFilterShow)
        lblFilterShow.backgroundColor = .clear
    }
    
    override var frame: CGRect {
        get {
            return super.frame
        }
        set (newFrame) {
            var frame =  newFrame
            frame.origin.x += 2
            frame.origin.y += 2
            frame.size.width -= 2 * 2
            frame.size.height -= 2 * 3
            super.frame = frame
        }
    }
    
}
