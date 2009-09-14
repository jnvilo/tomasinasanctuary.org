function toggle(whichOne, id){
    if (whichOne.getProperty('id') == id + "ExpandAll") {
        var navigation = $(id);
        var navigationULs = navigation.getElements('ul');
        var allImages = navigation.getElements('img');
        for (i = 0; i < navigationULs.length; i++) {
                navigationULs[i].removeClass('expand');
                navigationULs[i].addClass('contract');
                //navigationULs[i].setAttribute('class', 'contract');
                allImages[i].setProperty('src', 'modules/mod_artcats/tmpl/img/contract.gif');
        }
    }
    else if (whichOne.getProperty('id') == id + "CollapseAll"){
        var navigation = $(id);
        var navigationULs = navigation.getElements('ul');
        var allImages = navigation.getElements('img');
            for (i = 0; i < navigationULs.length; i++) {
                navigationULs[i].removeClass('contract');
                navigationULs[i].addClass('expand');
                //navigationULs[i].setAttribute('class', 'expand');
                allImages[i].setProperty('src', 'modules/mod_artcats/tmpl/img/expand.gif');
            }
    }
    else {
        var theParent = whichOne.getParent();
        var theParentULs = theParent.getElements('ul');
        var theParentImage = theParent.getElements('img');
        
        //Grab just the first UL and the first toggle image so that sub-sub UL navs/image don't expand too
        if (theParentULs[0].hasClass('expand')) {
            theParentULs[0].removeClass('expand');
            theParentULs[0].addClass('contract');
            //theParentULs[0].setAttribute('class', 'contract');
            theParentImage[0].setProperty('src', 'modules/mod_artcats/tmpl/img/contract.gif');
        }
        else {
            theParentULs[0].removeClass('contract');
            theParentULs[0].addClass('expand');
            //theParentULs[0].setAttribute('class', 'expand');
            theParentImage[0].setProperty('src', 'modules/mod_artcats/tmpl/img/expand.gif');
        }
    }
}