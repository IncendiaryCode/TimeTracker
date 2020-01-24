/*//////////////////////////////////////////////////////////////////////////////
 //
 //    Copyright (c) GreenPrint Technologies LLC. 2019
 //
 //    File Name         : InAppNotificationView.swift
 //
 //    File Created      : 24:Dec:2019
 //
 //    Dev Name          : Sachin Kumar K.
 //
 //    Description       : Inside app notification view.
 //                        (Required parameters: Message and Auto Dismiss)
 //
 //////////////////////////////////////////////////////////////////////////// */

import UIKit

class InAppNotificationView: UIView {
    private var lblMsg: UILabel!
    private var btnDismiss: UIButton!
    private var imgNotif: UIImageView!
    /// If auto dismiss notifiacation disabled then, user need to dismiss notifiction manually (Default always true).
    var autoDismiss = true
    /// setup delay time in seconds.
    var delaySec = 3
    
    init() {
        let screenBounds = UIScreen.main.bounds
        let cgRect = CGRect(x: 20, y: 40, width: screenBounds.width-40, height: 0)
        super.init(frame: cgRect)
        setup()
    }
    
    private override init(frame: CGRect) {
        super.init(frame: frame)
    }
    
    required init(coder aDecoder: NSCoder) {
        fatalError("init(coder:) has not been implemented")
    }
   
    /// Setup all views.
    private func setup() {
        let screenBounds = UIScreen.main.bounds
        
        // Set rounded corners.
        self.layer.cornerRadius = 20
        self.layer.masksToBounds = true
        self.backgroundColor = .gray
        
        // Setup message Image Notification.
        var cgRect = CGRect(x: 10, y: 20, width: 50, height: 50)
        imgNotif = UIImageView(frame: cgRect)
        imgNotif.image = UIImage(named: "notification.png")
        self.addSubview(imgNotif)
        
        // Setup label for message.
        let widthLabel = screenBounds.width - (180)
        cgRect = CGRect(x: imgNotif.frame.maxX + 10, y: 20, width: widthLabel, height: 60)
        lblMsg = UILabel(frame: cgRect)
        lblMsg.textColor = .white
        lblMsg.numberOfLines = 2
        lblMsg.font = lblMsg.font.withSize(13)
        self.addSubview(lblMsg)
        
        // Setup dismiss button.
        cgRect = CGRect(x: lblMsg.frame.maxX+10, y: 25, width: 44, height: 44)
        btnDismiss = UIButton(frame: cgRect)
        btnDismiss.imageEdgeInsets = UIEdgeInsets(top: 10, left: 10, bottom: 10, right: 10)
        btnDismiss.setTitle("", for: .normal)
        btnDismiss.setImage(UIImage(named: "dismiss.png"), for: .normal)
        btnDismiss.addTarget(self, action: #selector(btnDismissPressed(sender:)), for:
            .touchUpInside)
        self.addSubview(btnDismiss)
    }
    
    public func sendNotification(msg: String, autoDismiss: Bool? = nil) {
        var isDismissRequired = self.autoDismiss
        
        // If autodismiss parameter has
        if let dismiss = autoDismiss {
            isDismissRequired = dismiss
        }
        lblMsg.text = msg
        
        // If dismiss auto.
        if isDismissRequired {
            UIView.animate(withDuration: 0.5, animations: {
                // Animate while showing.
                self.frame.size = CGSize(width: self.frame.width, height: 100)
            }, completion: { _ in
                UIView.animate(withDuration: 0.5, delay: TimeInterval(self.delaySec), animations: {
                        // Delay.
                        // Hide animation.
                        self.frame.size = CGSize(width: self.frame.width, height: 0)
                }, completion: {_ in
                    self.removeFromSuperview()
                })
            })

        }
        else {
            UIView.animate(withDuration: 0.5, animations: {
                // Animate while showing.
                self.frame.size = CGSize(width: self.frame.width, height: 100)
            })
        }
    }
    
    @objc private func btnDismissPressed(sender: Any) {
        hideNotification()
    }
    
    public func hideNotification() {
        UIView.animate(withDuration: 0.5, animations: {
            // Hide animation.
            self.frame.size = CGSize(width: self.frame.width, height: 0)
        }, completion: {_ in
            self.removeFromSuperview()
        })
    }
}
