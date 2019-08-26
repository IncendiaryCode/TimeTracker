//
//  FilterProjectCell.swift
//  Attendance Manager
//
//  Created by Sachin on 9/25/19.
//  Copyright © 2019 Sachin. All rights reserved.
//

import UIKit

class FilterProjectCell: UITableViewCell {
    @IBOutlet weak var imgSelect: UIImageView!
    @IBOutlet weak var lblProjectName: UILabel!
    @IBOutlet weak var imgProjectLogo: UIImageView!
    
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }

}
