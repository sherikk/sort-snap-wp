
import React from "react";
import { Button } from "@/components/ui/button";
import { BookOpen, Github, HelpCircle } from "lucide-react";

const Header = () => {
  return (
    <header className="bg-white border-b border-gray-200">
      <div className="container mx-auto px-4">
        <div className="flex items-center justify-between h-16">
          <div className="flex items-center">
            <img 
              src="/logo.png" 
              alt="SortSnap Logo" 
              className="w-8 h-8 mr-2" 
              onError={(e) => {
                e.currentTarget.src = 'https://via.placeholder.com/32';
              }}
            />
            <h1 className="font-bold text-xl">SortSnap</h1>
          </div>
          
          <div className="flex space-x-2">
            <Button variant="outline" size="sm">
              <BookOpen className="h-4 w-4 mr-2" />
              Docs
            </Button>
            <Button variant="outline" size="sm">
              <HelpCircle className="h-4 w-4 mr-2" />
              Support
            </Button>
            <Button variant="outline" size="sm">
              <Github className="h-4 w-4 mr-2" />
              GitHub
            </Button>
          </div>
        </div>
      </div>
    </header>
  );
};

export default Header;
