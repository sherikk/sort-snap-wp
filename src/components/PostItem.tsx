
import React from "react";
import { Card, CardContent } from "@/components/ui/card";
import { GripVertical } from "lucide-react";
import { cn } from "@/lib/utils";

const PostItem = ({ post, provided, isDragging }) => {
  return (
    <div
      ref={provided.innerRef}
      {...provided.draggableProps}
      className={cn(
        "transition-all duration-200",
        isDragging ? "opacity-75 scale-105 z-10" : ""
      )}
    >
      <Card className={cn(
        "border mb-2", 
        isDragging ? "shadow-lg border-blue-300" : ""
      )}>
        <CardContent className="p-0">
          <div className="flex items-center p-4">
            <div
              {...provided.dragHandleProps}
              className="mr-3 text-gray-400 hover:text-gray-600 cursor-grab active:cursor-grabbing"
            >
              <GripVertical size={20} />
            </div>
            
            {post.thumbnail && (
              <div className="flex-shrink-0 mr-4">
                <img
                  src={post.thumbnail}
                  alt={post.title}
                  className="w-12 h-12 object-cover rounded"
                  onError={(e) => {
                    e.currentTarget.src = 'https://via.placeholder.com/48';
                  }}
                />
              </div>
            )}
            
            <div className="flex-grow min-w-0">
              <h3 className="font-medium text-blue-800 truncate">{post.title}</h3>
              <div className="flex flex-wrap gap-2 mt-1">
                <span className="text-xs bg-gray-100 px-2 py-1 rounded text-gray-600">
                  ID: {post.id}
                </span>
                {post.status && (
                  <span className={cn(
                    "text-xs px-2 py-1 rounded",
                    post.status === "publish" ? "bg-green-100 text-green-800" : "bg-amber-100 text-amber-800"
                  )}>
                    {post.status}
                  </span>
                )}
                {post.category && (
                  <span className="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
                    {post.category}
                  </span>
                )}
              </div>
            </div>
            
            <div className="ml-2 text-sm text-gray-500">
              {post.date && new Date(post.date).toLocaleDateString()}
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  );
};

export default PostItem;
